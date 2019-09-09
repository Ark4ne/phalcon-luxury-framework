<?php

namespace Neutrino\Foundation\Debug\Exceptions;

use Exception;
use InvalidArgumentException;
use Neutrino\Constants\Services;
use Neutrino\Debug\Exceptions\ExceptionHandlerInterface;
use Neutrino\Debug\Exceptions\Helper;
use Neutrino\Foundation\Debug\Exceptions\Renders\ConsoleRender;
use Neutrino\Foundation\Debug\Exceptions\Renders\WebRender;
use Neutrino\Foundation\Debug\Exceptions\Reporters\FlashReporter;
use Neutrino\Foundation\Debug\Exceptions\Reporters\LoggerReporter;
use Throwable;

abstract class ExceptionHandler implements ExceptionHandlerInterface
{
    /** @var \Phalcon\DiInterface */
    private static $container;

    /** @var string[]|\Neutrino\Foundation\Debug\Exceptions\ReporterInterface[] */
    private static $reporters = [
        LoggerReporter::class => LoggerReporter::class,
        FlashReporter::class => FlashReporter::class,
    ];

    private $ignores;

    /**
     * @param \Phalcon\DiInterface
     */
    final public static function attachContainer($container)
    {
        self::$container = $container;
    }

    /**
     * @param $reporter
     */
    final public static function attachReporter($reporter)
    {
        if (is_string($reporter)) {
            self::$reporters[$reporter] = $reporter;
        } elseif ($reporter instanceof ReporterInterface) {
            self::$reporters[get_class($reporter)] = $reporter;
        } else {
            throw new InvalidArgumentException('$reporter must be a string or instanceof ReporterInterface');
        }
    }

    /**
     * @param \Exception|\Throwable $throwable
     */
    final public function handle($throwable)
    {
        $this->safe('report', function () use ($throwable) {
            $this->report($throwable);
        });
        $this->safe('render', function () use ($throwable) {
            if (PHP_SAPI === 'cli') {
                $this->renderCli($throwable);
            } else {
                $this->renderWeb($throwable);
            }
        });
    }

    /**
     * @param \Exception|\Throwable $throwable
     */
    private function renderCli($throwable)
    {
        $this->renderConsole($throwable);
    }

    /**
     * @param \Exception|\Throwable $throwable
     */
    private function renderWeb($throwable)
    {
        if (!Helper::isFateful($throwable)) {
            return;
        }

        $request = null;
        $container = self::$container;

        if ($container && $container->has(Services::REQUEST)) {
            $request = $container->get(Services::REQUEST);
        }

        $response = $this->render($throwable, $request);

        foreach (ob_list_handlers() as $ob_handler) {
            ob_clean();
        }

        if (!headers_sent()) {
            $response->send();
        } else {
            echo $response->getContent();
        }
    }

    /**
     * @param string   $name
     * @param callable $callable
     */
    private function safe($name, $callable)
    {
        if (isset($this->ignores[$name])) {
            return;
        }

        try {
            $callable();
        } catch (Exception $e) {
        } catch (Throwable $e) {
        } finally {
            if (isset($e)) {
                $this->ignores[$name] = true;
                $this->handle($e);
                return;
            }
        }
    }

    /**
     * @param \Exception|\Throwable $throwable
     */
    public function report($throwable)
    {
        foreach (self::$reporters as $class => $reporter) {
            if (is_string($reporter)) {
                self::$reporters[$class] = $reporter = new $reporter;
            }

            $this->safe($class, function () use ($reporter, $throwable) {
                $reporter->report($throwable, self::$container);
            });
        }
    }

    /**
     * @param \Exception|\Throwable          $throwable
     * @param \Phalcon\Http\RequestInterface $request
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function render($throwable, $request = null)
    {
        return (new WebRender)->render($throwable, self::$container);
    }

    /**
     * @param \Exception|\Throwable $throwable
     */
    public function renderConsole($throwable)
    {
        (new ConsoleRender)->render($throwable, self::$container);
    }
}
