<?php

namespace Luxury\Providers;

use Luxury\Constants\Services;
use Phalcon\DiInterface;

/**
 * Class Cookies
 *
 * @package Luxury\Bootstrap\Services
 */
class Cookies extends Provider
{

    protected $name = Services::COOKIES;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return \Phalcon\Http\Response\Cookies
     */
    protected function register(DiInterface $di)
    {
        return new \Phalcon\Http\Response\Cookies;
    }
}
