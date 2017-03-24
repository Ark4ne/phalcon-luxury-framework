<?php

namespace Test\Stub;

use Neutrino\Constants\Services;
use Neutrino\Foundation\Micro\Kernel;
use Neutrino\Providers;

class StubKernelMicro extends Kernel
{
    protected $providers = [
        Providers\Url::class,
        Providers\Http\Router::class,
        Providers\Http\Dispatcher::class,
        Providers\Cache::class,
        Providers\Micro\Router::class,
    ];

    protected $listeners = [
        StubListener::class
    ];

    protected $middlewares = [
        //StubMiddleware::class
    ];

    public function registerRoutes()
    {
        /** @var \Neutrino\Micro\Router $router */
        $router = $this->{Services::MICRO_ROUTER};

        $router->addGet('get.test.abc', function () {
            return 'get.test.abc';
        });
    }
}