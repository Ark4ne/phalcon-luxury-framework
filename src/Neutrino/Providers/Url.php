<?php

namespace Neutrino\Providers;

use Neutrino\Constants\Services;


/**
 * Class Url
 *
 *  @package Neutrino\Foundation\Bootstrap
 */
class Url extends Provider
{
    protected $name = Services::URL;

    protected $shared = true;

    protected $aliases = [\Phalcon\Mvc\Url::class];

    /**
     * The URL component is used to generate all kind of urls in the application
     *
     * @return \Phalcon\Mvc\Url
     */
    protected function register()
    {
        $url = new \Phalcon\Mvc\Url();

        $url->setBaseUri($this->{Services::CONFIG}->app->base_uri);

        return $url;
    }
}
