<?php

namespace Neutrino\Error;

use Neutrino\Support\Arr;
use Phalcon\Logger;

/**
 * Class Helper
 *
 * @deprecated
 *
 * @package     Neutrino\Error
 */
class Helper
{
    /**
     * @deprecated
     * @param Error $error
     *
     * @return string
     */
    public static function format(Error $error)
    {
        return implode("\n", self::formatLines($error));
    }

    /**
     * @deprecated
     * @param Error $error
     * @param int   $pass
     *
     * @return array
     */
    private static function formatLines(Error $error, $pass = 0)
    {
        $pass++;

        $lines[] = self::getErrorType($error->type);
        if ($error->isException) {
            $lines[] = '  Class : ' . get_class($error->exception);
            $lines[] = '  Code : ' . $error->code;
        }

        $lines[] = '  Message : ' . $error->message;

        $lines[] = ' in : ' . str_replace(DIRECTORY_SEPARATOR, '/', $error->file) . '(' . $error->line . ')';

        if ($error->isException) {
            $lines[] = '';

            foreach (self::formatExceptionTrace($error->exception) as $trace) {
                $lines[] = '#' . $trace['id'] . ' ' . $trace['func'];

                $row = str_repeat(' ', strlen($trace['id']) + 2) . 'in : ';
                if (isset($trace['file'])) {
                    $row .= str_replace(DIRECTORY_SEPARATOR, '/', $trace['file']);
                    if (isset($trace['line'])) {
                        $row .= '(' . $trace['line'] . ')';
                    }
                } else {
                    $row .= '[internal function]';
                }

                $lines[] = $row;
            }

            $previous = $error->exception->getPrevious();

            if (!is_null($previous)) {
                $lines[] = '';
                $lines[] = '# Previous exception : ' . $pass;
                $lines[] = '';

                $lines = array_merge($lines, self::formatLines(Error::fromException($previous), $pass));
            }
        }

        return $lines;
    }

    /**
     * @deprecated
     * @param \Exception $exception
     *
     * @return array
     */
    public static function formatExceptionTrace($exception)
    {
        $traces = [];

        foreach ($exception->getTrace() as $idx => $trace) {
            $_trace = [];

            $_trace['id'] = $idx;

            $_trace['func'] = '';
            if (isset($trace['class'])) {
                $_trace['func'] = $trace['class'] . '->';
            }
            if (isset($trace['function'])) {
                $_trace['func'] .= $trace['function'];
            }

            $args = [];
            if (isset($trace['args'])) {
                $args = self::verboseArgs((array) $trace['args']);
            }
            $_trace['func'] .= '(' . implode(', ', $args) . ')';

            if (isset($trace['file'])) {
                $_trace['file'] = str_replace(DIRECTORY_SEPARATOR, '/', $trace['file']);
                $_trace['where'] = $_trace['file'];

                if (isset($trace['line'])) {
                    $_trace['line'] = $trace['line'];
                    $_trace['where'] .= '(' . $trace['line'] . ')';
                }
            } else {
                $_trace['where'] = '[internal function]';
            }

            $traces[] = $_trace;
        }

        return $traces;
    }

    /**
     * @deprecated
     * @param array $args
     *
     * @return array
     */
    public static function verboseArgs(array $args)
    {
        $arguments = [];

        foreach ($args as $key => $arg) {
            $arguments[$key] = self::verboseType($arg);
        }

        return $arguments;
    }

    /**
     * @deprecated
     * @param     $value
     * @param int $lvl
     *
     * @return mixed|string
     */
    public static function verboseType($value, $lvl = 0)
    {
        switch ($type = gettype($value)) {
            case 'array':
                if (!empty($value) && $lvl === 0) {
                    $found = [];
                    foreach ($value as $item) {
                        $type = gettype($item);
                        if ($type == 'object') {
                            $type = get_class($item);
                        }
                        $found[$type] = true;
                    }

                    $cfound = count($found);
                    $cvalue = count($value);

                    if ($cfound === 1 && !is_scalar($item) && $cvalue < 3
                        || $cfound === 1 && is_scalar($item) && $cvalue < 6
                        || $cfound > 1 && $cvalue < 5) {
                        $str = [];
                        if (Arr::isAssoc($value)) {
                            foreach ($value as $key => $item) {
                                $str[] = var_export($key, true) . " => " . self::verboseType($item, $lvl + 1);
                            }
                        } else {
                            foreach ($value as $item) {
                                $str[] = self::verboseType($item, $lvl + 1);
                            }
                        }

                        return 'array(' . implode(', ', $str) . ')';
                    }

                    if (count($found) === 1) {
                        return 'array.<' . $type . '>[' . count($value) . ']';
                    }

                    return 'array[' . count($value) . ']';
                }

                return 'array';
            case 'object':
                $class = explode('\\', get_class($value));
                return 'object(' . array_pop($class) . ')';
            case 'NULL':
                return 'null';
            case 'unknown type':
                return '?';
            case 'resource':
            case 'resource (closed)':
                return $type;
            case 'string':
                if (constant('BASE_PATH') && $value !== BASE_PATH . '\\') {
                    $value = str_replace(BASE_PATH . '\\', '', $value);
                }
                if (strlen($value) > 20) {
                    return "'" . substr($value, 0, 8) . '...' . substr($value, -8) . '\'[' . strlen($value) . ']';
                }
                return "'" . $value . "'";
            case 'boolean':
            case 'integer':
            case 'double':
            default:
                return var_export($value, true);
        }
    }

    /**
     * @deprecated
     * Maps error code to a string.
     *
     * @param int|string $code
     *
     * @return string
     */
    public static function getErrorType($code)
    {
        switch ($code) {
            case -1:
                return 'Uncaught exception';
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
        }

        return "(unknown error bit $code)";
    }

    /**
     * @deprecated
     * @param $code
     *
     * @return string
     */
    public static function verboseErrorType($code)
    {
        switch ($code) {
            case -1:
                return 'Uncaught exception';
            case E_COMPILE_ERROR:
            case E_CORE_ERROR:
            case E_ERROR:
            case E_PARSE:
            case E_RECOVERABLE_ERROR:
            case E_USER_ERROR:
                return 'Fatal error [' . self::getErrorType($code) . ']';
            case E_WARNING:
            case E_USER_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
                return 'Warning [' . self::getErrorType($code) . ']';
            case E_NOTICE:
            case E_USER_NOTICE:
                return 'Notice [' . self::getErrorType($code) . ']';
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return 'Deprecated [' . self::getErrorType($code) . ']';
        }

        return "(unknown error bit $code)";
    }

    /**
     * @deprecated
     * Maps error code to a log type.
     *
     * @param  integer $code
     *
     * @return integer
     */
    public static function getLogType($code)
    {
        switch ($code) {
            case E_PARSE:
                return Logger::CRITICAL;
            case E_COMPILE_ERROR:
            case E_CORE_ERROR:
            case E_ERROR:
                return Logger::EMERGENCY;
            case -1 : // Exception
            case E_RECOVERABLE_ERROR:
            case E_USER_ERROR:
                return Logger::ERROR;
            case E_WARNING:
            case E_USER_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
                return Logger::WARNING;
            case E_NOTICE:
            case E_USER_NOTICE:
                return Logger::NOTICE;
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return Logger::INFO;
        }

        return Logger::ERROR;
    }
}
