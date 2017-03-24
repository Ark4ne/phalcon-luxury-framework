<?php

namespace Neutrino\Config;

use Phalcon\Config;

/**
 * Class Loader
 *
 * @package     Neutrino\Config
 */
class Loader
{
    /**
     * @param string $basePath
     * @param array  $excludes
     *
     * @return \Phalcon\Config
     */
    public static function load($basePath, array $excludes = [])
    {
        if (!is_null($config = self::fromCompile($basePath))) {
            return $config;
        } else {
            return self::fromFiles($basePath, $excludes);
        }
    }

    /**
     * @param string $basePath
     * @param array  $excludes
     *
     * @return array
     */
    public static function raw($basePath, array $excludes = [])
    {
        $config = [];

        $excludes = array_flip($excludes);

        foreach (glob($basePath . '/config/*.php') as $file) {
            if (!isset($excludes[$fileName = basename($file, '.php')])) {
                $config[$fileName] = require $file;
            }
        }

        return $config;
    }

    /**
     * @param string $basePath
     * @param array  $excludes
     *
     * @return \Phalcon\Config
     */
    public static function fromFiles($basePath, array $excludes = [])
    {
        return new Config(self::raw($basePath, $excludes));
    }

    /**
     * @param string $basePath
     *
     * @return null|\Phalcon\Config
     */
    public static function fromCompile($basePath)
    {
        if (file_exists($compilePath = $basePath . '/bootstrap/compile/config.php')) {
            return new Config(require $compilePath);
        }

        return null;
    }
}
