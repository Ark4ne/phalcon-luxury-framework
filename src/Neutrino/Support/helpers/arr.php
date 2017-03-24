<?php

/**
 * Determine whether the given value is array accessible.
 *
 * @param mixed $value
 *
 * @return bool
 */
function arr_accessible($value)
{
    return is_array($value) || $value instanceof \ArrayAccess;
}

/**
 * Add an element to an array using "dot" notation if it doesn't exist.
 *
 * @param array  $array
 * @param string $key
 * @param mixed  $value
 *
 * @return array
 */
function arr_add($array, $key, $value)
{
    if (is_null(arr_get($array, $key))) {
        arr_set($array, $key, $value);
    }

    return $array;
}

/**
 * Collapse an array of arrays into a single array.
 *
 * @param \ArrayAccess|array $array
 *
 * @return array
 */
function arr_collapse($array)
{
    $results = [];

    foreach ($array as $values) {
        if (!arr_accessible($values)) {
            continue;
        }

        $results = array_merge($results, $values);
    }

    return $results;
}

/**
 * Divide an array into two arrays. One with keys and the other with values.
 *
 * @param array $array
 *
 * @return array
 */
function arr_divide($array)
{
    return [array_keys($array), array_values($array)];
}

/**
 * Flatten a multi-dimensional associative array with dots.
 *
 * @param array  $array
 * @param string $prepend
 *
 * @return array
 */
function arr_dot($array, $prepend = '')
{
    $results = [];

    foreach ($array as $key => $value) {
        if (is_array($value) && !empty($value)) {
            $results = array_merge($results, arr_dot($value, $prepend . $key . '.'));
        } else {
            $results[$prepend . $key] = $value;
        }
    }

    return $results;
}

/**
 * Get all of the given array except for a specified array of items.
 *
 * @param array        $array
 * @param array|string $keys
 *
 * @return array
 */
function arr_except($array, $keys)
{
    arr_forget($array, $keys);

    return $array;
}

/**
 * Determine if the given key exists in the provided array.
 *
 * @param \ArrayAccess|array $array
 * @param string|int         $key
 *
 * @return bool
 */
function arr_exists($array, $key)
{
    if (is_array($array)) {
        return isset($array[$key]) || array_key_exists($key, $array);
    }

    return $array->offsetExists($key);
}

/**
 * Return the first element in an array passing a given truth test.
 *
 * @param array         $array
 * @param callable|null $callback
 * @param mixed         $default
 *
 * @return mixed
 */
function arr_first($array, $callback = null, $default = null)
{
    if (is_null($callback)) {
        return empty($array) ? obj_value($default) : reset($array);
    }

    foreach ($array as $key => $value) {
        if (call_user_func($callback, $key, $value)) {
            return $value;
        }
    }

    return obj_value($default);
}

/**
 * Return the last element in an array passing a given truth test.
 *
 * @param array         $array
 * @param callable|null $callback
 * @param mixed         $default
 *
 * @return mixed
 */
function arr_last($array, callable $callback = null, $default = null)
{
    if (is_null($callback)) {
        return empty($array) ? obj_value($default) : end($array);
    }

    return arr_first(array_reverse($array, true), $callback, $default);
}

/**
 * Flatten a multi-dimensional array into a single level.
 *
 * @param array $array
 * @param int   $depth
 *
 * @return array
 */
function arr_flatten($array, $depth = INF)
{
    return array_reduce($array, function ($result, $item) use ($depth) {
        if (!is_array($item)) {
            return array_merge($result, [$item]);
        } elseif ($depth === 1) {
            return array_merge($result, array_values($item));
        } else {
            return array_merge($result, arr_flatten($item, $depth - 1));
        }
    }, []);
}

/**
 * Remove one or many array items from a given array using "dot" notation.
 *
 * @param array        $array
 * @param array|string $keys
 *
 * @return void
 */
function arr_forget(&$array, $keys)
{
    $original = &$array;
    $keys     = (array)$keys;
    if (count($keys) === 0) {
        return;
    }
    foreach ($keys as $key) {
        // if the exact key exists in the top-level, remove it
        if (arr_exists($array, $key)) {
            unset($array[$key]);
            continue;
        }
        $parts = explode('.', $key);
        // clean up before each pass
        $array = &$original;
        while (count($parts) > 1) {
            $part = array_shift($parts);
            if (isset($array[$part]) && is_array($array[$part])) {
                $array = &$array[$part];
            } else {
                continue 2;
            }
        }
        unset($array[array_shift($parts)]);
    }
}

/**
 * Get an item from an array.
 *
 * @param array  $array
 * @param string $key
 * @param mixed  $default
 *
 * @return mixed
 */
function arr_read($array, $key, $default = null)
{
    if (is_null($key)) {
        return $default;
    }

    if (isset($array[$key]) || array_key_exists($key, $array)) {
        return $array[$key];
    }

    return $default;
}

/**
 * Get an item from an array.
 *
 * @param array  $array
 * @param string $key
 * @param mixed  $default
 *
 * @return mixed
 */
function arr_fetch($array, $key, $default = null)
{
    if (is_null($key)) {
        return $default;
    }

    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Get an item from an array using "dot" notation.
 *
 * @param \ArrayAccess|array $array
 * @param string             $key
 * @param mixed              $default
 *
 * @return mixed
 */
function arr_get($array, $key, $default = null)
{
    if (!arr_accessible($array)) {
        return obj_value($default);
    }
    if (is_null($key)) {
        return $array;
    }
    if (!is_array($key)) {
        if (arr_exists($array, $key)) {
            return $array[$key];
        }

        $keys = explode('.', $key);
    } else {
        $keys = $key;
    }
    foreach ($keys as $segment) {
        if (arr_accessible($array) && arr_exists($array, $segment)) {
            $array = $array[$segment];
        } else {
            return obj_value($default);
        }
    }

    return $array;
}

/**
 * Check if an item exists in an array using "dot" notation.
 *
 * @param \ArrayAccess|array $array
 * @param string|array       $keys
 *
 * @return bool
 */
function arr_has($array, $keys)
{
    if (is_null($keys)) {
        return false;
    }
    $keys = (array)$keys;
    if (!$array) {
        return false;
    }
    if ($keys === []) {
        return false;
    }
    foreach ($keys as $key) {
        $subKeyArray = $array;
        if (arr_exists($array, $key)) {
            continue;
        }
        foreach (explode('.', $key) as $segment) {
            if (arr_accessible($subKeyArray) && arr_exists($subKeyArray, $segment)) {
                $subKeyArray = $subKeyArray[$segment];
            } else {
                return false;
            }
        }
    }

    return true;
}

/**
 * Determines if an array is associative.
 *
 * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
 *
 * @param array $array
 *
 * @return bool
 */
function arr_isAssoc(array $array)
{
    $keys = array_keys($array);

    return array_keys($keys) !== $keys;
}

/**
 * Get a subset of the items from the given array.
 *
 * @param array        $array
 * @param array|string $keys
 *
 * @return array
 */
function arr_only($array, $keys)
{
    return array_intersect_key($array, array_flip((array)$keys));
}

/**
 * Pluck an array of values from an array.
 *
 * @param \ArrayAccess|array $array
 * @param string|array       $value
 * @param string|array|null  $key
 *
 * @return array
 */
function arr_pluck($array, $value, $key = null)
{
    $results = [];

    list($value, $key) = arr_explodePluckParameters($value, $key);

    foreach ($array as $item) {
        $itemValue = arr_get($item, $value);

        // If the key is "null", we will just append the value to the array and keep
        // looping. Otherwise we will key the array using the value of the key we
        // received from the developer. Then we'll return the final array form.
        if (is_null($key)) {
            $results[] = $itemValue;
        } else {
            $itemKey = arr_get($item, $key);

            $results[$itemKey] = $itemValue;
        }
    }

    return $results;
}

/**
 * Push an item onto the beginning of an array.
 *
 * @param array $array
 * @param mixed $value
 * @param mixed $key
 *
 * @return array
 */
function arr_prepend($array, $value, $key = null)
{
    if (is_null($key)) {
        array_unshift($array, $value);
    } else {
        $array = [$key => $value] + $array;
    }

    return $array;
}

/**
 * Get a value from the array, and remove it.
 *
 * @param array  $array
 * @param string $key
 * @param mixed  $default
 *
 * @return mixed
 */
function arr_pull(&$array, $key, $default = null)
{
    $value = arr_get($array, $key, $default);

    arr_forget($array, $key);

    return $value;
}

/**
 * Set an array item to a given value using "dot" notation.
 *
 * If no key is given to the method, the entire array will be replaced.
 *
 * @param array  $array
 * @param string $key
 * @param mixed  $value
 *
 * @return array
 */
function arr_set(&$array, $key, $value)
{
    if (is_null($key)) {
        return $array = $value;
    }

    $keys = explode('.', $key);

    while (count($keys) > 1) {
        $key = array_shift($keys);

        // If the key doesn't exist at this depth, we will just create an empty array
        // to hold the next value, allowing us to create the arrays to hold final
        // values at the correct depth. Then we'll keep digging into the array.
        if (!isset($array[$key]) || !is_array($array[$key])) {
            $array[$key] = [];
        }

        $array = &$array[$key];
    }

    $array[array_shift($keys)] = $value;

    return $array;
}

/**
 * Recursively sort an array by keys and values.
 *
 * @param array $array
 *
 * @return array
 */
function arr_sortRecursive($array)
{
    foreach ($array as &$value) {
        if (is_array($value)) {
            $value = arr_sortRecursive($value);
        }
    }

    if (arr_isAssoc($array)) {
        ksort($array);
    } else {
        sort($array);
    }

    return $array;
}

/**
 * Sort the array using the given callback.
 *
 * @param array    $array
 * @param callable $callback
 *
 * @return array
 *
 * function arr_sort($array, callable $callback)
 * {
 * return Collection::make($array)->sortBy($callback)->all();
 * }/**/

/**
 * Filter the array using the given callback.
 *
 * @param array    $array
 * @param callable $callback
 *
 * @return array
 */
function arr_where($array, callable $callback)
{
    return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
}

/**
 * Explode the "value" and "key" arguments passed to "pluck".
 *
 * @param string|array      $value
 * @param string|array|null $key
 *
 * @return array
 */
function arr_explodePluckParameters($value, $key)
{
    $value = is_string($value) ? explode('.', $value) : $value;

    $key = is_null($key) || is_array($key) ? $key : explode('.', $key);

    return [$value, $key];
}
    