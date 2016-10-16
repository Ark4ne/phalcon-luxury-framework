<?php

namespace Luxury\Providers\Cli;

use Luxury\Constants\Services;
use Luxury\Providers\Provider;
use Phalcon\DiInterface;
use Phalcon\Security;

/**
 * Class Dispatcher
 *
 * @package Luxury\Bootstrap\Services
 */
class Dispatcher extends Provider
{
    protected $name = Services::DISPATCHER;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return \Phalcon\Cli\Dispatcher
     */
    protected function register(DiInterface $di)
    {
        /* @var \Phalcon\Di $this */
        $dispatcher = new \Phalcon\Cli\Dispatcher();

        // Create an events manager
        $eventsManager = $di->getShared(Services::EVENTS_MANAGER);

        // Assign the events manager to the dispatcher
        $dispatcher->setEventsManager($eventsManager);

        $dispatcher->setDefaultNamespace('\App\Cli\Tasks');

        return $dispatcher;
    }
}
