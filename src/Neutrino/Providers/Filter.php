<?php

namespace Neutrino\Providers;

use Neutrino\Constants\Services;


/**
 * Class Filter
 *
 *  @package Neutrino\Providers
 */
class Filter extends BasicProvider
{
    protected $class = \Phalcon\Filter::class;

    protected $name = Services::FILTER;

    protected $shared = true;

    protected $aliases = [\Phalcon\Filter::class];
}
