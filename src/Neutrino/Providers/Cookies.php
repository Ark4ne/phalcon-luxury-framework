<?php

namespace Neutrino\Providers;

use Neutrino\Constants\Services;


/**
 * Class Cookies
 *
 *  @package Neutrino\Providers
 */
class Cookies extends BasicProvider
{
    protected $class = \Phalcon\Http\Response\Cookies::class;

    protected $name = Services::COOKIES;

    protected $shared = true;

    protected $aliases = [\Phalcon\Http\Response\Cookies::class];
}
