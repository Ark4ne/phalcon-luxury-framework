<?php

namespace Luxury\Support\Facades;

use Mockery;
use Mockery\MockInterface;
use Phalcon\DiInterface;
use RuntimeException;

/**
 * Class Facade
 *
 * @see Laravel 5.2 Illuminate\Support\Facades\Facade
 *
 * @package Luxury\Support\Facades
 */
abstract class Facade
{
    /**
     * @var \Phalcon\DiInterface
     */
    protected static $di;

    /**
     * The resolved object instances.
     *
     * @var array
     */
    protected static $resolvedInstance;

    public static function clearResolvedInstances()
    {
        self::$resolvedInstance = [];
    }

    /**
     * @param \Phalcon\DiInterface $di
     */
    public static function setDependencyInjection(DiInterface $di)
    {
        static::$di = $di;
    }

    /**
     * Hotswap the underlying instance behind the facade.
     *
     * @param  mixed $instance
     *
     * @return void
     */
    public static function swap($instance)
    {
        self::$resolvedInstance[static::getFacadeAccessor()] = $instance;

        static::$di->setShared(static::getFacadeAccessor(), $instance);
    }

    /**
     * Initiate a mock expectation on the facade.
     *
     * @param mixed ...
     *
     * @return \Mockery\Expectation
     */
    public static function shouldReceive()
    {
        $name = static::getFacadeAccessor();
        if (static::isMock()) {
            $mock = self::$resolvedInstance[$name];
        } else {
            $mock = static::createFreshMockInstance();
        }

        return $mock->shouldReceive(...func_get_args());
    }

    /**
     * Get the root object behind the facade.
     *
     * @return mixed
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string $method
     * @param  array  $args
     *
     * @return mixed
     * @throws RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (!$instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        if (empty($args)) {
            return $instance->$method();
        } else {
            return $instance->$method(...$args);
        }
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     * @throws RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        throw new RuntimeException('Facade does not implement getFacadeAccessor method.');
    }

    /**
     * Create a fresh mock instance.
     *
     * @return MockInterface
     */
    protected static function createFreshMockInstance()
    {
        $name = static::getFacadeAccessor();

        self::$resolvedInstance[$name] = $mock = static::createMockInstance();

        $mock->shouldAllowMockingProtectedMethods();

        if (isset(static::$di)) {
            static::$di->setShared($name, $mock);
        }

        return $mock;
    }

    /**
     * Create a fresh mock instance.
     *
     * @return MockInterface
     */
    protected static function createMockInstance()
    {
        $class = static::getMockableClass();

        return $class ? Mockery::mock($class) : Mockery::mock();
    }

    /**
     * Determines whether a mock is set as the instance of the facade.
     *
     * @return bool
     */
    protected static function isMock()
    {
        $name = static::getFacadeAccessor();

        return isset(self::$resolvedInstance[$name]) && self::$resolvedInstance[$name] instanceof MockInterface;
    }

    /**
     * Get the mockable class for the bound instance.
     *
     * @return string|null
     */
    protected static function getMockableClass()
    {
        if ($root = static::getFacadeRoot()) {
            return get_class($root);
        }

        return null;
    }

    /**
     * Resolve the facade root instance from the container.
     *
     * @param  string|object $name
     *
     * @return mixed
     * @throws RuntimeException
     */
    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }

        if (isset(self::$resolvedInstance[$name])) {
            return self::$resolvedInstance[$name];
        }

        return self::$resolvedInstance[$name] = static::$di->getShared($name);
    }
}
