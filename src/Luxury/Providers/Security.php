<?php

namespace Luxury\Providers;

use Luxury\Constants\Services;
use Phalcon\DiInterface;

/**
 * Class Security
 *
 * @package Luxury\Bootstrap\Services
 */
class Security extends Provider
{
    protected $name = Services::SECURITY;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return \Luxury\Security\SecurityPlugin|mixed
     */
    protected function register(DiInterface $di)
    {
        return new \Luxury\Security\SecurityPlugin;
    }
}
