<?php

namespace Luxury\Support\DesignPatterns;

/**
 * Class Adapter
 *
 * @package Luxury
 */
abstract class Strategy
{
    /**
     * Supported Adapters
     *
     * @var array
     */
    protected $supported;

    /**
     * Default Adapter
     *
     * @var string
     */
    protected $default;

    /**
     * All registered Adapters
     *
     * @var array
     */
    private $adapters = [];

    /**
     * Current Adapter
     *
     * @var mixed
     */
    private $adapter;

    /**
     * Return / Change current adapter.
     *
     * @param string|null $use
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function uses($use = null)
    {
        if (!empty($use)) {
            if (!in_array($use, $this->supported)) {
                throw new \RuntimeException(static::class . " : $use unsupported. ");
            }
            if (!isset($this->adapters[$use])) {
                $this->adapters[$use] = $this->make($use);
            }
            $this->adapter = $this->adapters[$use];
        }

        if (empty($this->adapter)) {
            $this->adapter = $this->adapters[$this->default] = $this->make($this->default);
        }

        return $this->adapter;
    }

    /**
     * Make the instance & return them
     *
     * @param string $use
     *
     * @return mixed
     */
    protected function make($use)
    {
        return new $use;
    }
}
