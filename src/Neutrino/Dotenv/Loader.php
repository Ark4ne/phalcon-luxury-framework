<?php

namespace Neutrino\Dotenv;

use Neutrino\Dotenv;

class Loader
{
    private static $has_putenv;

    /**
     * Loads environment variables from .env.php to getenv(), $_ENV, and $_SERVER automatically.
     *
     * @param string $path Path to ".env.php" files
     *
     * @return bool
     * @throws \Neutrino\Dotenv\Exception\InvalidFileException
     */
    public static function load($path)
    {
        if (!file_exists($path . DIRECTORY_SEPARATOR . '.env.php')) {
            return false;
        }


        /** @noinspection PhpIncludeInspection */
        $config = require $path . DIRECTORY_SEPARATOR . '.env.php';

        if (isset($config['APP_ENV']) && ($env = $config['APP_ENV']) && !empty($env)) {
            if (file_exists($path . DIRECTORY_SEPARATOR . '.env.' . $env . '.php')) {
                /** @noinspection PhpIncludeInspection */
                $config = array_merge($config, require $path . DIRECTORY_SEPARATOR . '.env.' . $env . '.php');
            }
        }

        foreach ($config as $key => $value) {
            Dotenv::put($key, $value);
        }

        return true;
    }

    public static function hasPutenv()
    {
        if (isset(self::$has_putenv)) {
            return self::$has_putenv;
        }

        return self::$has_putenv = function_exists('putenv');
    }
}