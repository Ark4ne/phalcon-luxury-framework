<?php

namespace Luxury\Providers;

use Luxury\Constants\Services;
use Phalcon\Annotations\Adapter\Memory as AnnotationsMemory;
use Phalcon\DiInterface;

/**
 * Class Annotations
 *
 * @package Luxury\Bootstrap\Services
 */
class Annotations extends Provider
{
    protected $name = Services::ANNOTATIONS;

    protected $shared = true;

    /**
     * @param \Phalcon\DiInterface $di
     *
     * @return \Phalcon\Annotations\Adapter\Memory
     */
    protected function register(DiInterface $di)
    {
        return new AnnotationsMemory;
    }
}
