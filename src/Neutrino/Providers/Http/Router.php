<?php

namespace Neutrino\Providers\Http;

use Neutrino\Constants\Services;
use Neutrino\Providers\BasicProvider;


/**
 * Class Router
 *
 * @package Neutrino\Foundation\Bootstrap
 */
class Router extends BasicProvider
{
    protected $class = \Phalcon\Mvc\Router::class;

    protected $name = Services::ROUTER;

    protected $shared = true;

    protected $aliases = [\Phalcon\Mvc\Router::class];

    protected $options = [
        'arguments' => [
            ['type'  => 'parameter',
             'value' => false]
        ],
        'calls'     => [
            ['method'    => 'setUriSource',
             'arguments' => [
                 ['type'  => 'parameter',
                 'value' => \Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI]
             ]]
        ]
    ];
}
