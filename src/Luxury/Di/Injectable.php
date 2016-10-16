<?php

namespace Luxury\Di;

/**
 * Class Injectable
 *
 * @package Luxury\Di
 *
 * @property-read \Phalcon\Cache\BackendInterface cache
 * @property-read \Phalcon\Mvc\Application        app
 */
abstract class Injectable extends \Phalcon\Di\Injectable
{
    /**
     * Injectable constructor.
     *
     * Apply Di::getDefault() into injectable.
     */
    public function __construct()
    {
        $this->setDI($this->getDI());
    }
}
