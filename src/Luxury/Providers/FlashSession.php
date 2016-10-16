<?php

namespace Luxury\Providers;

use Luxury\Constants\Services;
use Phalcon\DiInterface;
use Phalcon\Flash\Session;

/**
 * Class FlashSession
 *
 * @package Luxury\Providers
 */
class FlashSession extends Provider
{
    protected $name = Services::FLASH_SESSION;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return \Phalcon\Flash\Session
     */
    protected function register(DiInterface $di)
    {
        return new Session;
    }
}
