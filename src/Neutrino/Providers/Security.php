<?php

namespace Neutrino\Providers;

use Neutrino\Constants\Services;

/**
 * Class Security
 *
 *  @package Neutrino\Providers
 */
class Security extends BasicProvider
{
    protected $class = \Phalcon\Security::class;

    protected $name = Services::SECURITY;

    protected $shared = true;

    protected $aliases = [\Phalcon\Security::class];
}
