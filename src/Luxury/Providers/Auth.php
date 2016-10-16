<?php

namespace Luxury\Providers;

use Luxury\Auth\AuthManager;
use Luxury\Constants\Services;
use Phalcon\DiInterface;

/**
 * Class Auth
 *
 * @package     Luxury\Providers
 */
class Auth extends Provider
{

    protected $name = Services::AUTH;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return \Luxury\Auth\AuthManager
     */
    protected function register(DiInterface $di)
    {
        return new AuthManager();
    }
}
