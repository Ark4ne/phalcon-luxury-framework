<?php

namespace Neutrino\Micro;

use Phalcon\Mvc\Micro\MiddlewareInterface;

abstract class Middleware implements MiddlewareInterface
{
    const ON_BEFORE = 'before';
    const ON_AFTER = 'after';
    const ON_FINISH = 'finish';

    /**
     * Return on witch event we bind the middleware
     *
     * @return string
     */
    public abstract function bindOn();
}
