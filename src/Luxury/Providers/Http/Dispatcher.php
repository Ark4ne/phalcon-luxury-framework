<?php

namespace Luxury\Providers\Http;

use Luxury\Constants\Services;
use Luxury\Providers\Provider;
use Phalcon\DiInterface;

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
     * @return \Phalcon\Mvc\Dispatcher
     */
    protected function register(DiInterface $di)
    {
        /* @var \Phalcon\Di $this */
        $dispatcher = new \Phalcon\Mvc\Dispatcher();

        // Create an events manager
        $eventsManager = $di->getShared(Services::EVENTS_MANAGER);

        // Listen for events produced in the dispatcher using the Security plugin
        $eventsManager->attach(
            'dispatch:beforeExecuteRoute',
            $di->getShared(Services::SECURITY)
        );

        // Assign the events manager to the dispatcher
        $dispatcher->setEventsManager($eventsManager);

        return $dispatcher;
    }
}
