<?php

namespace Neutrino\Providers\Http;

use Neutrino\Constants\Services;
use Neutrino\Providers\BasicProvider;

/**
 * Class Dispatcher
 *
 * @package Neutrino\Providers
 */
class Dispatcher extends BasicProvider
{
    protected $class = \Phalcon\Mvc\Dispatcher::class;

    protected $name = Services::DISPATCHER;

    protected $shared = true;

    protected $aliases = [\Phalcon\Mvc\Dispatcher::class];

    protected $options = [
        'calls' => [
            ['method'    => 'setEventsManager',
             'arguments' => [
                 ['type' => 'service',
                  'name' => Services::EVENTS_MANAGER]
             ]]
        ]
    ];
}
