<?php

namespace Test\Stub;

use Luxury\Foundation\Application\Http as HttpApplication;
use Luxury\Providers;

/**
 * Class TestKernel
 */
class StubKernel extends HttpApplication
{
    /**
     * Return the Provider List to load.
     *
     * @var string[]
     */
    protected $providers = [
        /*
         * Basic Configuration
         */
        //LoggerProvider::class,
        //UrlProvider::class,
        //FlashProvider::class,
        //SessionProvider::class,
        Providers\Http\Router::class,
        //ViewProvider::class,
        Providers\Http\Dispatcher::class,
        Providers\Cache::class,
        //DatabaseProvider::class,
        /*
         * Service provided by the Phalcon\Di\FactoryDefault
         *
        \Luxury\Providers\Models::class,
        \Luxury\Providers\Cookies::class,
        \Luxury\Providers\Filter::class,
        \Luxury\Providers\Escaper::class,
        \Luxury\Providers\Security::class,
        \Luxury\Providers\Crypt::class,
        \Luxury\Providers\Annotations::class,
        /**/
    ];

    /**
     * Return the Middleware List to load.
     *
     * @var string[]
     */
    protected $middlewares = [];

    /**
     * Register the routes of the application.
     */
    public function registerRoutes()
    {
    }
}
