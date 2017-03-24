<?php

namespace Neutrino\Interfaces;

use Phalcon\Config;

/**
 * Interface KernelInterface
 *
 *  @package Neutrino\Interfaces
 *
 * @property-read Config|\stdClass|array config
 */
interface Kernelable
{
    /**
     * Application starter
     *
     * @param Config $config
     *
     * @return void
     */
    public function bootstrap(Config $config);

    /**
     * Register the services.
     *
     * @return void
     */
    public function registerServices();

    /**
     * Register the routes.
     *
     * @return void
     */
    public function registerRoutes();

    /**
     * Register the middlewares.
     *
     * @return void
     */
    public function registerMiddlewares();

    /**
     * Register the events listeners.
     *
     * @return void
     */
    public function registerListeners();

    /**
     * Register the modules.
     *
     * @param array $modules
     * @param bool  $merge
     *
     * @return
     */
    public function registerModules(array $modules, $merge = false);

    /**
     * @return void
     */
    public function boot();
}
