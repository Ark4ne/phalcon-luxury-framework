<?php

namespace Neutrino\Providers;

use Neutrino\Constants\Services;

/**
 * Class Crypt
 *
 *  @package Neutrino\Providers
 */
class Crypt extends BasicProvider
{
    protected $class = \Phalcon\Crypt::class;

    protected $name = Services::CRYPT;

    protected $shared = true;

    protected $aliases = [\Phalcon\Crypt::class];
}
