<?php

namespace Neutrino\Providers;

use Neutrino\Auth\Manager as AuthManager;
use Neutrino\Constants\Services;

/**
 * Class Auth
 *
 *  @package Neutrino\Providers
 */
class Auth extends BasicProvider
{
    protected $class = AuthManager::class;

    protected $name = Services::AUTH;

    protected $shared = true;

    protected $aliases = [AuthManager::class];
}
