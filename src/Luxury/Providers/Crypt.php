<?php

namespace Luxury\Providers;

use Luxury\Constants\Services;
use Phalcon\DiInterface;

/**
 * Class Crypt
 *
 * @package Luxury\Bootstrap\Services
 */
class Crypt extends Provider
{
    protected $name = Services::CRYPT;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return \Phalcon\Crypt
     */
    protected function register(DiInterface $di)
    {
        return new \Phalcon\Crypt;
    }
}
