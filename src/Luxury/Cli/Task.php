<?php

namespace Luxury\Cli;

use Luxury\Cli\Output\ConsoleOutput;
use Luxury\Cli\Output\Decorate;
use Luxury\Cli\Output\Table;
use Luxury\Constants\Events;
use Luxury\Foundation\Cli\HelperTask;
use Luxury\Support\Arr;
use Phalcon\Cli\Task as PhalconTask;
use Phalcon\Events\Event;

/**
 * Class Task
 *
 * @package Luxury\Cli
 *
 * @property-read \Luxury\Cache\CacheStrategy            $cache
 * @property-read \Phalcon\Mvc\Application               $app
 * @property-read \Phalcon\Config|\stdClass|\ArrayAccess $config
 * @property-read \Luxury\Cli\Router                     $router
 * @property-read \Phalcon\Cli\Dispatcher                $dispatcher
 */
class Task extends PhalconTask
{
    /**
     * @var ConsoleOutput
     */
    protected $output;

    public function onConstruct()
    {
        $this->output = new ConsoleOutput($this->hasOption('q', 'quiet'));

        $this->dispatcher
            ->getEventsManager()
            ->attach(Events\Dispatch::BEFORE_EXCEPTION, function (Event $event, $dispatcher, \Exception $exception) {
                return $this->handleException($event, $dispatcher, $exception);
            });

        if (($this->hasOption('s', 'stats')) && !$this->dispatcher->wasForwarded()) {
            $this->dispatcher
                ->getEventsManager()
                ->attach(Events\Cli\Application::AFTER_HANDLE, function () {
                    $this->displayStats();
                });
        }

        $this->dispatcher
        ->getEventsManager()
        ->attach(Events\Cli\Application::AFTER_HANDLE, function () {
            $this->output->clean();
        });
    }

    /**
     * Handle help option & forward to HelperTask
     *
     * @return bool
     */
    public function beforeExecuteRoute()
    {
        if ($this->hasOption('h', 'help')) {
            $this->dispatcher->forward([
                'task'   => HelperTask::class,
                'action' => 'main',
                'params' => [
                    'task'   => $this->dispatcher->getHandlerClass(),
                    'action' => $this->dispatcher->getActionName(),
                ]
            ]);

            return false;
        }

        return true;
    }

    /**
     * Handle Exception and output them.
     *
     * @param Event      $event
     * @param            $dispatcher
     * @param \Exception $exception
     *
     * @return bool
     */
    public function handleException(Event $event, $dispatcher, \Exception $exception)
    {
        $this->error('Exception : ' . get_class($exception));
        $this->error($exception->getMessage());
        $this->error($exception->getTraceAsString());

        return false;
    }

    public function displayStats()
    {
        $this->line('');
        $this->line('Stats : ');
        $this->line("\tmem:" . Decorate::info(memory_get_usage()));
        $this->line("\tmem.peak:" . Decorate::info(memory_get_peak_usage()));
        $this->line("\ttime:" . Decorate::info((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])));
    }

    public function line($str)
    {
        $this->output->write($str, true);
    }

    public function info($str)
    {
        $this->output->info($str);
    }

    public function notice($str)
    {
        $this->output->notice($str);
    }

    public function warn($str)
    {
        $this->output->warn($str);
    }

    public function error($str)
    {
        $this->output->error($str);
    }

    public function question($str)
    {
        $this->output->question($str);
    }

    public function table(array $datas, array $headers = [], $style = Table::STYLE_DEFAULT)
    {
        (new Table($this->output, $datas, $headers, $style))->display();
    }


    /**
     * Return all agruments pass to the cli
     *
     * @return array
     */
    protected function getArgs()
    {
        return $this->dispatcher->getParams();
    }

    /**
     * Return an arg by his name, or default
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return array
     */
    protected function getArg($name, $default = null)
    {
        return Arr::fetch($this->getArgs(), $name, $default);
    }

    /**
     * Check if arg has been passed
     *
     * @param string $name
     *
     * @return bool
     */
    protected function hasArg($name)
    {
        return Arr::has($this->getArgs(), $name);
    }

    /**
     * Return all options pass to the cli
     *
     * @return array
     */
    protected function getOptions()
    {
        return $this->dispatcher->getOptions();
    }

    /**
     * Return an option by his name, or default
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return array
     */
    protected function getOption($name, $default = null)
    {
        return Arr::fetch($this->getOptions(), $name, $default);
    }

    /**
     * Check if option has been passed
     *
     * @param string[] ...$options
     *
     * @return bool
     */
    protected function hasOption(...$options)
    {
        foreach ($options as $option) {
            if (Arr::has($this->getOptions(), $option)) {
                return true;
            }
        }

        return false;
    }
}
