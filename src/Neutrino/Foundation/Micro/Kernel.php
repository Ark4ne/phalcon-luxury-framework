<?php

namespace Neutrino\Foundation\Micro;

use Neutrino\Dotenv;
use Neutrino\Foundation\Kernelize;
use Neutrino\Interfaces\Kernelable;
use Neutrino\Micro\Middleware;
use Phalcon\Di\FactoryDefault as Di;
use Phalcon\Mvc\Micro as MicroKernel;

abstract class Kernel extends MicroKernel implements Kernelable
{
    use Kernelize;

    /**
     * Return the Provider List to load.
     *
     * @var string[]
     */
    protected $providers = [];

    /**
     * Return the Middlewares to attach onto the application.
     *
     * @var string[]
     */
    protected $middlewares = [];

    /**
     * Return the Events Listeners to attach onto the application.
     *
     * @var string[]
     */
    protected $listeners = [];

    /**
     * The DependencyInjection class to use.
     *
     * @var string
     */
    protected $dependencyInjection = Di::class;

    /**
     * This methods registers the middlewares to be used by the application
     */
    public function registerMiddlewares()
    {
        foreach ($this->middlewares as $middleware) {
            $this->registerMiddleware(new $middleware);
        }
    }

    /**
     * @param \Neutrino\Micro\Middleware $middleware
     */
    protected function registerMiddleware(Middleware $middleware)
    {
        $on = $middleware->bindOn();

        if ($on === 'before' || $on === 'after' || $on === 'finish')
            $this->$on($middleware);
        else
            throw new \RuntimeException(__METHOD__ . ': ' . get_class($middleware) . ' can\'t bind on "' . $on . '"');
    }

    /**
     * @override
     *
     * @param array $modules
     * @param bool  $merge
     */
    final public function registerModules(array $modules, $merge = false)
    {

    }

    /**
     * Register the routes.
     *
     * @return void
     */
    public function registerRoutes()
    {
        require Dotenv::env('BASE_PATH') .'/routes/micro.php';
    }

    /**
     * @return void
     */
    public function boot()
    {
        // TODO: Implement boot() method.
    }
}