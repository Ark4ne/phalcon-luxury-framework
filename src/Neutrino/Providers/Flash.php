<?php

namespace Neutrino\Providers;

use Neutrino\Constants\Services;

use Phalcon\Flash\Direct as FlashDirect;

/**
 * Class Flash
 *
 * @package Neutrino\Foundation\Bootstrap
 */
class Flash extends BasicProvider
{
    protected $class = FlashDirect::class;

    protected $name = Services::FLASH;

    protected $shared = false;

    protected $aliases = [FlashDirect::class];

    protected $options = [
        'arguments' => [
            ['type'  => 'parameter',
             'value' => [
                 'error'   => 'alert alert-danger',
                 'success' => 'alert alert-success',
                 'notice'  => 'alert alert-info',
                 'warning' => 'alert alert-warning'
             ]]
        ],
        'calls'     => [
            [
                'method'    => 'setImplicitFlush',
                'arguments' => [
                    [
                        'type'  => 'parameter',
                        'value' => false,
                    ]
                ]
            ],
        ]
    ];
}
