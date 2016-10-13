<?php

namespace Luxury\Test;

use Luxury\Foundation\Kernelize;
use Mockery;
use Phalcon\Application;
use Phalcon\Config;
use Phalcon\Config as PhConfig;
use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\DiInterface;
use Phalcon\Escaper;
use Phalcon\Mvc\Url;
use PHPUnit_Framework_TestCase as UnitTestCase;

/**
 * Class UnitTestCase
 *
 * @package Phalcon\Test
 */
abstract class TestCase extends UnitTestCase implements InjectionAwareInterface
{
    /**
     * Holds the configuration variables and other stuff
     * I can use the DI container but for tests like the Translate
     * we do not need the overhead
     *
     * @var Config|null
     */
    protected $config;

    /**
     * @var Application|Kernelize
     */
    protected $app;

    /**
     * @var \Luxury\Foundation\Application
     */
    protected $lxApp;

    /**
     * @var Application|Kernelize
     */
    protected static $appGlobal;

    /**
     * @var \Luxury\Foundation\Application
     */
    protected static $lxAppGlobal;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        global $config;

        parent::setUp();

        $this->checkExtension('phalcon');

        // Creating the application
        $this->lxApp = new \Luxury\Foundation\Application(new PhConfig($config));
        $this->app   = $this->lxApp->make($this->kernel());
    }

    /**
     * @return string
     */
    abstract protected function kernel();

    /**
     * @return Application
     */
    protected function globalApp()
    {
        global $config;

        if (self::$appGlobal == null) {
            self::$lxAppGlobal = new \Luxury\Foundation\Application(new PhConfig($config));
            self::$appGlobal   = self::$lxAppGlobal->make($this->kernel());
        }

        return self::$appGlobal;
    }

    protected function tearDown()
    {
        Mockery::close();
        $this->app->getDI()->reset();
        $this->app = null;

        parent::tearDown();
    }

    /**
     * Checks if a particular extension is loaded and if not it marks
     * the tests skipped
     *
     * @param mixed $extension
     */
    public function checkExtension($extension)
    {
        $message = function ($ext) {
            sprintf('Warning: %s extension is not loaded', $ext);
        };

        if (is_array($extension)) {
            foreach ($extension as $ext) {
                if (!extension_loaded($ext)) {
                    $this->markTestSkipped($message($ext));
                    break;
                }
            }
        } elseif (!extension_loaded($extension)) {
            $this->markTestSkipped($message($extension));
        }
    }

    /**
     * Returns a unique file name
     *
     * @param  string $prefix A prefix for the file
     * @param  string $suffix A suffix for the file
     *
     * @return string
     */
    protected function getFileName($prefix = '', $suffix = 'log')
    {
        $prefix = ($prefix) ? $prefix . '_' : '';
        $suffix = ($suffix) ? $suffix : 'log';

        return uniqid($prefix, true) . '.' . $suffix;
    }

    /**
     * Removes a file from the system
     *
     * @param string $path
     * @param string $fileName
     */
    protected function cleanFile($path, $fileName)
    {
        $file = (substr($path, -1, 1) != "/") ? ($path . '/') : $path;
        $file .= $fileName;

        $actual = file_exists($file);

        if ($actual) {
            unlink($file);
        }
    }

    /**
     * Sets the Config object.
     *
     * @param Config $config
     *
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Returns the Config object if any.
     *
     * @return null|Config
     */
    public function getConfig()
    {
        if (!$this->config instanceof Config && $this->getDI()->has('config')) {
            return $this->getDI()->getShared('config');
        }

        return $this->config;
    }

    /**
     * Sets the Dependency Injector.
     *
     * @see    Injectable::setDI
     *
     * @param  DiInterface $di
     *
     * @return $this
     */
    public function setDI(DiInterface $di)
    {
        return $this->app->setDI($di);
    }

    /**
     * Returns the internal Dependency Injector.
     *
     * @see    Injectable::getDI
     * @return DiInterface
     */
    public function getDI()
    {
        return $this->app->getDI();
    }
}
