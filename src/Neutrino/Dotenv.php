<?php

namespace Neutrino;

use Neutrino\Dotenv\Exception\InvalidFileException;
use Neutrino\Dotenv\Loader;

class Dotenv
{
    /**
     * @var array
     */
    private static $cache = [];

    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param string $key
     * @param mixed  $default
     * @param bool   $fresh
     *
     * @return bool|null|string
     */
    public static function env($key, $default = null, $fresh = false)
    {
        if (!$fresh && (isset(self::$cache[$key]) || array_key_exists($key, self::$cache))) {
            return self::$cache[$key];
        }

        $value = self::getenv($key);

        if (is_null($value)) {
            return $default;
        }

        return self::trans($value);
    }

    /**
     * Put a value to environment variables.
     *
     * @param string $key
     * @param string $value
     *
     * @throws \Neutrino\Dotenv\Exception\InvalidFileException
     */
    public static function put($key, $value)
    {
        if (!is_string($value)) {
            throw new InvalidFileException('Neutrino\Dotenv support only string value.');
        }

        if (Loader::hasPutenv()) {
            putenv($key . '=' . $value);
        }

        self::$cache[$key] = self::trans($_ENV[$key] = $value);
    }

    /**
     * @param string $key
     *
     * @return null|string
     */
    private static function getenv($key)
    {
        $value = getenv($key);

        if ($value !== false) {
            return $value;
        }

        return isset($_ENV[$key]) ? $_ENV[$key] : null;
    }

    private static function trans($value)
    {
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }
}