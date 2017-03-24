<?php

namespace Neutrino\Foundation;

use Phalcon\Config;

/**
 * Class Application
 *
 * Phalcon Application Bootstrapper
 *
 * @package Neutrino\Foundation
 */
class Bootstrap
{
    /**
     * @var \Phalcon\Config
     */
    private $config;

    /**
     * Application constructor.
     *
     * @param \Phalcon\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param $kernelClass
     *
     * @return \Phalcon\Application
     */
    public function make($kernelClass)
    {
        /** @var \Phalcon\Application|\Neutrino\Interfaces\Kernelable $kernel */
        $kernel = new $kernelClass;

        $kernel->bootstrap($this->config);

        $kernel->registerServices();
        $kernel->registerMiddlewares();
        $kernel->registerListeners();
        $kernel->registerRoutes();
        $kernel->registerModules([]);

        $kernel->boot();

        return $kernel;
    }
}
