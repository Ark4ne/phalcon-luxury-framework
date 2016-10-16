<?php

namespace Luxury\Providers\Http;

use Luxury\Constants\Services;
use Luxury\Providers\Provider;
use Phalcon\DiInterface;

/**
 * Class Router
 *
 * @package Luxury\Foundation\Bootstrap
 */
class Router extends Provider
{
    protected $name = Services::ROUTER;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return \Phalcon\Mvc\Router
     */
    protected function register(DiInterface $di)
    {
        $router = new \Phalcon\Mvc\Router(false);

        $router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);

        return $router;
    }
}
