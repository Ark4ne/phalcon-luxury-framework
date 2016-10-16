<?php

namespace Test\TestCase;

/**
 * Interface TestListenable
 */
interface TestListenable
{
    /**
     * @param string     $seek
     * @param array|null $data
     *
     * @return void
     */
    public function view($seek, array $data = null);

    /**
     * @param string $seek
     *
     * @return bool
     */
    public function hasView($seek);

    /**
     * @param string $seek
     *
     * @return array|null
     */
    public function getView($seek);
}
