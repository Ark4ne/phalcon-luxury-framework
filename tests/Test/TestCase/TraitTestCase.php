<?php

namespace Test\TestCase;

use Test\Stub\StubKernel;

/**
 * Class TraitTestCase
 */
trait TraitTestCase
{
    /**
     * @return mixed
     */
    protected function kernel()
    {
        return StubKernel::class;
    }
}
