<?php

namespace Neutrino\Providers;

use Neutrino\Constants\Services;

use Phalcon\Flash\Session;

/**
 * Class FlashSession
 *
 *  @package Neutrino\Providers
 */
class FlashSession extends BasicProvider
{
    protected $class = Session::class;

    protected $name = Services::FLASH_SESSION;

    protected $shared = true;

    protected $aliases = [Session::class];
}
