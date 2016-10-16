<?php

namespace Luxury\Providers;

use Luxury\Constants\Services;
use Phalcon\DiInterface;

/**
 * Class Filter
 *
 * @package Luxury\Bootstrap\Services
 */
class Filter extends Provider
{
    protected $name = Services::FILTER;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return mixed|\Phalcon\Filter
     */
    protected function register(DiInterface $di)
    {
        return new \Phalcon\Filter;
    }
}
