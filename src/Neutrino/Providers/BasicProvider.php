<?php

namespace Neutrino\Providers;

use Neutrino\Interfaces\Providable;
use Phalcon\Di\Injectable;
use Phalcon\Di\Service;

/**
 * Class BasicProvider
 *
 * @package Neutrino\Providers
 *
 * @property-read \Phalcon\Application|\Phalcon\Mvc\Application|\Phalcon\Cli\Console|\Phalcon\Mvc\Micro $application
 * @property-read \Phalcon\Config|\stdClass|\ArrayAccess                                                $config
 */
abstract class BasicProvider extends Injectable implements Providable
{
    /**
     * Class to provide
     *
     * @var string
     */
    protected $class;

    /**
     * Name of the service
     *
     * @var string
     */
    protected $name;

    /**
     * Aliases
     *
     * @var string[]
     */
    protected $aliases;

    /**
     * Shared Service
     *
     * @var bool
     */
    protected $shared = false;

    /**
     * Options to pass to the definition
     *
     * @var array
     */
    protected $options;

    /**
     * BasicProvider constructor.
     */
    final public function __construct()
    {
        if (empty($this->name) || !is_string($this->name)) {
            throw new \RuntimeException('BasicProvider "' . static::class . '::$name" isn\'t valid.');
        }
        if (empty($this->class) || !is_string($this->class)) {
            throw new \RuntimeException('BasicProvider "' . static::class . '::$class" isn\'t valid.');
        }
    }

    public function registering()
    {
        if (empty($this->options)) {
            $definition = $this->class;
        } else {
            $definition = array_merge(['className' => $this->class], $this->options);
        }

        $service = new Service($this->name, $definition, $this->shared);

        $this->getDI()->setRaw($this->name, $service);

        if (!empty($this->aliases))
            foreach ($this->aliases as $alias) {
                $this->getDI()->setRaw($alias, $service);
            }
    }
}
