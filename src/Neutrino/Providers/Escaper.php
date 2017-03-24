<?php

namespace Neutrino\Providers;

use Neutrino\Constants\Services;


/**
 * Class Escaper
 *
 *  @package Neutrino\Providers
 */
class Escaper extends BasicProvider
{
    protected $class = \Phalcon\Escaper::class;

    protected $name = Services::ESCAPER;

    protected $shared = true;

    protected $aliases = [\Phalcon\Escaper::class];
}
