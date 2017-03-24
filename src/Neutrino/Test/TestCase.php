<?php

namespace Neutrino\Test;

use Mockery;
use Neutrino\Foundation\Kernelize;
use Neutrino\Support\Facades\Facade;
use Phalcon\Config;
use Phalcon\Config as PhConfig;
use Phalcon\Di\InjectionAwareInterface;
use Phalcon\DiInterface;
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
     * @var \Phalcon\Application|\Phalcon\Cli\Console|\Neutrino\Foundation\Kernelize
     */
    protected $app;

    /**
     * @var \Neutrino\Foundation\Bootstrap
     */
    protected $lxApp;

    /**
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        global $config;

        parent::setUp();

        $this->checkExtension('phalcon');

        // Creating the application
        $this->lxApp = new \Neutrino\Foundation\Bootstrap(new PhConfig($config));
        $this->app   = $this->lxApp->make($this->kernel());
    }

    /**
     * @return string
     */
    protected function kernel()
    {
        return static::kernelClassInstance();
    }

    /**
     * @return string
     */
    protected static function kernelClassInstance()
    {
        throw new \RuntimeException("kernelClassInstance not implemented.");
    }

    protected function tearDown()
    {
        Mockery::close();
        Facade::clearResolvedInstances();
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
     * @return DiInterface
     */
    public function getDI()
    {
        return $this->app->getDI();
    }

    /**
     * @param string $className
     * @param string $propertyName
     *
     * @return \ReflectionProperty
     */
    public function getPrivateProperty($className, $propertyName)
    {
        $reflector = new \ReflectionClass($className);
        $property  = $reflector->getProperty($propertyName);

        $property->setAccessible(true);

        return $property;
    }

    /**
     * @param string $className
     * @param string $methodName
     *
     * @return \ReflectionMethod
     */
    public function getPrivateMethod($className, $methodName)
    {
        $reflection = new \ReflectionClass($className);
        $method     = $reflection->getMethod($methodName);

        $method->setAccessible(true);

        return $method;
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object      &$object    Instantiated object that we will run method on.
     * @param string      $methodName Method name to call
     * @param array       $parameters Array of parameters to pass into method.
     * @param string|null $className
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [], $className = null)
    {
        return $this->getPrivateMethod(
            !is_null($className) ? $className : get_class($object),
            $methodName
        )->invokeArgs($object, $parameters);
    }

    /**
     * Call protected/private static method of a class.
     *
     * @param string $class
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeStaticMethod($class, $methodName, array $parameters = [])
    {
        return $this->getPrivateMethod(
            $class,
            $methodName
        )->invokeArgs(null, $parameters);
    }

    /**
     * Get value of an private/protected property of an object.
     *
     * @param object $object
     * @param string $propertyName
     * @param null   $className
     *
     * @return mixed
     */
    public function getValueProperty(&$object, $propertyName, $className = null)
    {
        return $this->getPrivateProperty(
            $className ? $className : get_class($object),
            $propertyName
        )->getValue($object);
    }

    /**
     * Get value of an private/protected property of an object.
     *
     * @param string $class
     * @param string $propertyName
     *
     * @return mixed
     */
    public function getStaticValueProperty($class, $propertyName)
    {
        return $this->getPrivateProperty(
            $class,
            $propertyName
        )->getValue(null);
    }

    /**
     * Set value of an private/protected static property of a class.
     *
     * @param object $object
     * @param string $propertyName
     * @param mixed  $value
     * @param null   $className
     */
    public function setValueProperty(&$object, $propertyName, $value, $className = null)
    {
        $this->getPrivateProperty(
            $className ? $className : get_class($object),
            $propertyName
        )->setValue($object, $value);
    }

    /**
     * Set value of an private/protected property of a class.
     *
     * @param string $class
     * @param string $propertyName
     * @param mixed  $value
     */
    public function setStaticValueProperty($class, $propertyName, $value)
    {
        $this->getPrivateProperty(
            $class,
            $propertyName
        )->setValue(null, $value);
    }
}
