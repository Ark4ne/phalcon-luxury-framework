<?php

namespace Neutrino\Cli\Output;

use Neutrino\Version;

final class Helper
{
    private static $reflections = [];

    /**
     * Remove the decoration of a string
     *
     * @param $string
     *
     * @return mixed
     */
    public static function removeDecoration($string)
    {
        return preg_replace("/\033\\[[^m]*m/", '', $string);
    }

    /**
     * Return the real len of a string (without decoration)
     *
     * @param $string
     *
     * @return int
     */
    public static function strlenWithoutDecoration($string)
    {
        return self::strlen(self::removeDecoration($string));
    }

    public static function strlen($string)
    {
        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return strlen($string);
        }

        return mb_strwidth($string, $encoding);
    }

    /**
     * Correct str_pad wrong output when str is decorate
     *
     * @param string $str
     * @param int    $size
     * @param string $pad
     * @param int    $type
     *
     * @return string
     */
    public static function strPad($str, $size, $pad, $type = STR_PAD_RIGHT)
    {
        $washLen = self::strlenWithoutDecoration($str);

        switch ($type) {
            case STR_PAD_BOTH:
                $m = $size - $washLen;

                return str_repeat($pad, floor($m / 2)) . $str . str_repeat($pad, ceil($m / 2));
            case STR_PAD_LEFT:
                return str_repeat($pad, $size - $washLen) . $str;
            case STR_PAD_RIGHT:
            default:
                return $str . str_repeat($pad, $size - $washLen);
        }
    }

    /**
     * @param \Phalcon\Cli\Router\Route|\Phalcon\Mvc\Router\Route $route
     *
     * @return string
     */
    public static function describeRoutePattern($route)
    {
        $paths = $route->getPaths();

        $compiled = $route->getCompiledPattern();
        if ($compiled !== $route->getPattern()) {
            foreach ($paths as $key => $value) {
                if (in_array($key, ['controller', 'task', 'action', 'middleware'])) {
                    continue;
                }
                if (is_int($value)) {
                    $compiled = preg_replace('/\([^?][^\/\)]+\)/', Decorate::notice('{' . $key . '}'), $compiled, 1);
                }
            }
            preg_match('/\^(.+)\$/', $compiled, $matchs);
            $compiled = $matchs[1];
        }

        return $compiled;
    }

    /**
     * @param $class
     * @param $methodName
     *
     * @return array
     */
    public static function getTaskInfos($class, $methodName)
    {
        $infos = [];
        $reflection = self::getReflection($class);

        try {
            $method = $reflection->getMethod($methodName);
        } catch (\Exception $e) {

        }
        $description = '';
        if (!empty($method)) {
            $docBlock = $method->getDocComment();

            preg_match_all('/\*\s*@(\w+)(.*)/', $docBlock, $annotations);
            $docBlock = preg_replace('/\*\s*@(\w+)(.*)/', '', $docBlock);

            foreach ($annotations[1] as $k => $annotation) {
                switch ($annotation) {
                    case 'description':
                        $infos['description'] = trim($annotations[2][$k]);
                        break;
                    case 'argument':
                    case 'option':
                        $infos[$annotation . 's'][] = trim($annotations[2][$k]);
                        break;
                }
            }

            if (empty($infos['description'])) {
                preg_match_all('/\*([^\n\r]+)/', $docBlock, $lines);

                foreach ($lines[1] as $line) {
                    $line = trim($line);
                    if ($line == '*' || $line == '/') {
                        continue;
                    }
                    $description .= $line . ' ';
                }

                $infos['description'] = trim($description);
            }
        }

        return $infos;
    }

    /**
     * @return string
     */
    public static function neutrinoVersion()
    {
        return Decorate::info('Neutrino framework') . ' ' . Decorate::notice('v' . Version::get() . ' ['. Version::getId().']');
    }

    /**
     * @param string $class
     *
     * @return \ReflectionClass
     */
    private static function getReflection($class)
    {
        if (!arr_has(self::$reflections, $class)) {
            self::$reflections[$class] = new \ReflectionClass($class);
        }

        return self::$reflections[$class];
    }
}
