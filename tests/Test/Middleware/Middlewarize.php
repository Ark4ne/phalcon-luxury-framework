<?php

namespace Test\Middleware;

use Phalcon\Events\Event;

/**
 * Class Middlewarize
 *
 * @package     Middleware
 */
trait Middlewarize
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
    public function init(Event $event, $source, $data = null)
    {
        $this->view(__FUNCTION__, [$event, $source, $data]);
    }

    /**
     * Called before the execution of handler
     *
     * @param \Phalcon\Events\Event $event
     * @param \Phalcon\Dispatcher|mixed   $source
     * @param mixed|null                  $data
     *
     * @throws \Exception
     * @return bool
     */
    public function before(Event $event, $source, $data = null)
    {
        $this->view(__FUNCTION__, [$event, $source, $data]);
    }

    /**
     * Called after the execution of handler
     *
     * @param \Phalcon\Events\Event $event
     * @param \Phalcon\Dispatcher|mixed   $source
     * @param mixed|null                  $data
     *
     * @throws \Exception
     * @return bool
     */
    public function after(Event $event, $source, $data = null)
    {
        $this->view(__FUNCTION__, [$event, $source, $data]);
    }

    /**
     * Called before the execution of handler
     *
     * @param \Phalcon\Events\Event $event
     * @param \Phalcon\Dispatcher|mixed   $source
     * @param mixed|null                  $data
     *
     * @throws \Exception
     * @return bool
     */
    public function finish(Event $event, $source, $data = null)
    {
        $this->view(__FUNCTION__, [$event, $source, $data]);
    }
}
