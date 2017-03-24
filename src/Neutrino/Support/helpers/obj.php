<?php

/**
 * Fill in data where it's missing.
 *
 * @param  mixed        $target
 * @param  string|array $key
 * @param  mixed        $value
 *
 * @return mixed
 */
function obj_fill(&$target, $key, $value)
{
    return obj_set($target, $key, $value, false);
}

/**
 * @param      $object
 * @param      $property
 * @param null $default
 *
 * @return null
 */
function obj_read($object, $property, $default = null)
{
    if (is_null($object)) {
        return obj_value($default);
    }

    if (isset($object->$property) || property_exists($object, $property)) {
        return $object->$property;
    }

    return obj_value($default);
}

/**
 * @param      $object
 * @param      $property
 * @param null $default
 *
 * @return null
 */
function obj_fetch($object, $property, $default = null)
{
    if (is_null($object)) {
        return obj_value($default);
    }

    return isset($object->$property) ? $object->$property : obj_value($default);
}

/**
 * Get an item from an array or object using "dot" notation.
 *
 * @param  mixed        $target
 * @param  string|array $key
 * @param  mixed        $default
 *
 * @return mixed
 */
function obj_get($target, $key, $default = null)
{
    if (is_null($key) || !is_object($target)) {
        return obj_value($default);
    }

    if (!is_array($key)) {
        if (isset($target->{$key}) || property_exists($target, $key)) {
            return $target->{$key};
        }

        $keys = explode('.', $key);
    } else {
        $keys = $key;
    }
    foreach ($keys as $segment) {
        if (is_object($target) && isset($target->{$segment})) {
            $target = $target->{$segment};
        } else {
            return obj_value($default);
        }
    }

    return $target;
}

/**
 * Set an item on an array or object using dot notation.
 *
 * @param  mixed        $target
 * @param  string|array $key
 * @param  mixed        $value
 * @param  bool         $overwrite
 *
 * @return mixed
 */
function obj_set(&$target, $key, $value, $overwrite = true)
{
    if (is_null($key)) {
        return $target;
    }

    if (!is_array($key)) {
        if (isset($target->{$key}) || property_exists($target, $key)) {
            if ($overwrite) {
                $target->{$key} = obj_value($value);
            }

            return $target;
        }

        $keys = explode('.', $key);
    } else {
        $keys = $key;
    }

    $keep = $target;

    while (count($keys) > 1) {
        $key = array_shift($keys);

        // If the key doesn't exist at this depth, we will just create an empty array
        // to hold the next value, allowing us to create the arrays to hold final
        // values at the correct depth. Then we'll keep digging into the array.
        if (!isset($target->{$key}) || !is_object($target->{$key}) && $overwrite) {
            $target->{$key} = new \stdClass;
        } elseif (!is_object($target->{$key})) {
            return $target;
        }

        $target = &$target->{$key};
    }


    $key = array_shift($keys);
    if (!isset($target->{$key}) || $overwrite) {
        $target->{$key} = obj_value($value);
    }

    return $keep;
}

/**
 * Return the default value of the given value.
 *
 * @param  mixed $value
 *
 * @return mixed
 */
function obj_value($value)
{
    return $value instanceof Closure ? $value() : $value;
}
