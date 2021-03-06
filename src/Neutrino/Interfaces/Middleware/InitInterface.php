<?php

namespace Neutrino\Interfaces\Middleware;

use Phalcon\Events\Event;

/**
 * Interface InitMiddleware
 *
 *  @package Neutrino\Middleware
 *
 * Middleware finish handled
 */
interface InitInterface
{
    /**
     * Called on the initialization
     *
     * @param \Phalcon\Events\Event $event
     * @param \Phalcon\Dispatcher|mixed   $source
     * @param mixed|null                  $data
     *
     * @throws \Exception
     * @return bool
     */
    public function init(Event $event, $source, $data = null);
}
