<?php

namespace Luxury\Security;

use Luxury\Constants\Services;
use Luxury\Di\Injectable;

/**
 * Class RateLimiter
 *
 * @see https://github.com/laravel/framework/blob/5.2/src/Illuminate/Cache/RateLimiter.php
 *
 * @package Luxury\Security
 */
class RateLimiter extends Injectable
{
    /**
     * Cache key suffix for the flag "too many attempts"
     *
     * @var string
     */
    private $klock = '.lockout';

    /**
     * Determine if the given key has been "accessed" too many times.
     *
     * @param  string $key
     * @param  int    $maxAttempts
     * @param  int    $decaySeconds
     *
     * @return bool
     */
    public function tooManyAttempts($key, $maxAttempts, $decaySeconds = 1)
    {
        /** @var \Luxury\Cache\CacheStrategy $cache */
        $cache = $this->getDI()->getShared(Services::CACHE);

        if ($cache->exists($key . $this->klock, $decaySeconds)) {
            return true;
        }
        if ($this->attempts($key, $decaySeconds) > $maxAttempts) {
            $cache->save($key . $this->klock, time() + ($decaySeconds), $decaySeconds);

            $this->resetAttempts($key);

            return true;
        }

        return false;
    }

    /**
     * Increment the counter for a given key for a given decay time.
     *
     * @param  string $key
     * @param  int    $decaySeconds
     *
     * @return int
     */
    public function hit($key, $decaySeconds = 1)
    {
        /** @var \Luxury\Cache\CacheStrategy $cache */
        $cache = $this->getDI()->getShared(Services::CACHE);

        if (!$cache->exists($key, $decaySeconds)) {
            $cache->save($key, 1, $decaySeconds);
        }

        $value = (int)$cache->get($key, $decaySeconds);

        $value++;
        $cache->save($key, $value, $decaySeconds);

        return $value;
    }

    /**
     * Get the number of attempts for the given key.
     *
     * @param  string $key
     * @param  int    $decaySeconds
     *
     * @return mixed
     */
    public function attempts($key, $decaySeconds = 1)
    {
        return is_null($value = $this->getDI()->getShared(Services::CACHE)->get($key, $decaySeconds))
            ? 0
            : (int)$value;
    }

    /**
     * Reset the number of attempts for the given key.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function resetAttempts($key)
    {
        return $this->getDI()->getShared(Services::CACHE)->delete($key);
    }

    /**
     * Get the number of retries left for the given key.
     *
     * @param  string $key
     * @param  int    $maxAttempts
     * @param         $decaySeconds
     *
     * @return int
     */
    public function retriesLeft($key, $maxAttempts, $decaySeconds)
    {
        $attempts = $this->attempts($key, $decaySeconds);

        return $attempts === 0 ? $maxAttempts : $maxAttempts - $attempts + 1;
    }

    /**
     * Clear the hits and lockout for the given key.
     *
     * @param  string $key
     *
     * @return void
     */
    public function clear($key)
    {
        $this->resetAttempts($key);

        $this->getDI()->getShared(Services::CACHE)->delete($key . $this->klock);
    }

    /**
     * Get the number of seconds until the "key" is accessible again.
     *
     * @param  string $key
     * @param  int    $decaySeconds
     *
     * @return int
     */
    public function availableIn($key, $decaySeconds)
    {
        $time = $this->getDI()->getShared(Services::CACHE)->get($key . $this->klock, $decaySeconds);

        return $time - time();
    }
}
