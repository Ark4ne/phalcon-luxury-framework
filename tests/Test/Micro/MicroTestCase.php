<?php

namespace Test\Micro;

use Test\Stub\StubKernelMicro;

trait MicroTestCase
{
    protected static function kernelClassInstance()
    {
        return StubKernelMicro::class;
    }
}