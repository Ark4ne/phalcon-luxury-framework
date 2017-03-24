<?php

namespace Neutrino\Http;

use Neutrino\Constants\Services;

/**
 * Class Controller
 *
 *  @package Neutrino\Foundation
 *
 * @property-read \Phalcon\Application|\Phalcon\Mvc\Application|\Phalcon\Cli\Console|\Phalcon\Mvc\Micro $application
 * @property-read \Phalcon\Config|\stdClass|\ArrayAccess                                                $config
 */
abstract class Controller extends \Phalcon\Mvc\Controller
{
    /**
     * Event called on controller construction
     *
     * Register middleware here.
     */
    protected function onConstruct()
    {
        $this->routeMiddleware();
    }

    /**
     * Register middleware was attached in the route.
     */
    protected function routeMiddleware()
    {
        $router = $this->router;
        $dispatcher = $this->dispatcher;

        if (!$dispatcher->wasForwarded() && $router->wasMatched()) {
            $actionMethod = $dispatcher->getActionName();

            $route = $router->getMatchedRoute();

            $paths = $route->getPaths();

            if (!empty($paths['middleware'])) {
                $middlewares = $paths['middleware'];

                if (!is_array($middlewares)) {
                    $middlewares = [$middlewares];
                }

                foreach ($middlewares as $key => $middleware) {
                    if (is_int($key)) {
                        $middlewareClass  = $middleware;
                        $middlewareParams = [];
                    } else {
                        $middlewareClass  = $key;
                        $middlewareParams = !is_array($middlewares) ? [$middleware] : $middleware;
                    }

                    $this->middleware($middlewareClass, ...$middlewareParams)->only([$actionMethod]);
                }
            }
        }
    }

    /**
     * Attach a ControllerMiddleware.
     *
     * On controllers, only ControllerMiddleware are attachable,
     * because the middleware registration, passed by the controller, will made at the controller instantiation.
     * Because of this, the events
     *  "Application::boot"
     *  "Dispatch::beforeDispatchLoop"
     *  "Dispatch::BeforeDispatch"
     * can not be caught.
     *
     * @param string $middlewareClass
     * @param mixed  ...$params
     *
     * @return \Neutrino\Foundation\Middleware\Controller
     */
    protected function middleware($middlewareClass, ...$params)
    {
        $middleware = new $middlewareClass(static::class, ...$params);

        $this->{Services::APP}->attach($middleware);

        return $middleware;
    }
}
