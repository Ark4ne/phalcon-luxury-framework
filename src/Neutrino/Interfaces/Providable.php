<?php

namespace Neutrino\Interfaces;

/**
 * Class Providable
 *
 * @package Neutrino\Providers
 */
interface Providable
{
    /**
     * Called upon bootstrap the application.
     * Adds to container services desired services.
     *
     * @return void
     */
    public function registering();
}
