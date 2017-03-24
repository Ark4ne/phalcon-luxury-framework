<?php

namespace Neutrino\Providers\Micro;

use Neutrino\Constants\Services;
use Neutrino\Providers\BasicProvider;

class Router extends BasicProvider
{
    protected $class = \Neutrino\Micro\Router::class;

    protected $name = Services::MICRO_ROUTER;

    protected $shared = true;

    protected $aliases = [
        \Neutrino\Micro\Router::class
    ];
}