<?php

namespace Aybarsm\Support;

class Arr
{
    public static function isAssoc(array $array) : bool
    {
        return ! array_is_list($array);
    }

    public static function isList(array $array) : bool
    {
        return array_is_list($array);
    }

    public static function accessible(mixed $value) : bool
    {
        return is_array($value) || $value instanceof \ArrayAccess;
    }

    public static function exists(\ArrayAccess|array $array, string|int|float $key) : bool
    {
        if ($array instanceof \ArrayAccess) {
            return $array->offsetExists($key);
        }

        if (is_float($key)) $key = (string) $key;

        return array_key_exists($key, $array);
    }

    public static function get($array, $key, $default = null) : mixed
    {
        if (! static::accessible($array)) {
            return value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (! str_contains($key, '.')) {
            return $array[$key] ?? value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }

        return $array;
    }

    public static function set(array &$array, string|int|null $key, mixed $value) : array
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    public static function add(array $array, string|int|float $key, mixed $value) : array
    {
        if (is_null(static::get($array, $key))) {
            static::set($array, $key, $value);
        }

        return $array;
    }

    public static function collapse(iterable $array) : array
    {
        $results = [];

        foreach ($array as $values) {
            if (! is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        return array_merge([], ...$results);
    }

    public static function undot(iterable $array) : array
    {
        $results = [];

        foreach ($array as $key => $value) {
            static::set($results, $key, $value);
        }

        return $results;
    }

    public static function dot(iterable $array, array $opt = []) : array
    {
        $opt['prepend'] = (! is_string($opt['prepend'] ?? null) ? '' : $opt['prepend']);
        $opt['meta'] = ($opt['meta'] ?? null) === true;
        $opt['all'] = ($opt['all'] ?? null) === true;
        $opt['sub'] = ($opt['sub'] ?? null) === true;

        if (!$opt['sub']) {
            $opt['res'] = [];
            if ($opt['meta']) $opt['metaData'] = [];
        }

        foreach ($array as $key => $value) {
            if ($opt['all']) $opt['res']["{$opt['prepend']}{$key}"] = [];

            if ($opt['meta']) {
                $opt['metaData']["{$opt['prepend']}{$key}"] = [
                    'type' => gettype($value),
                        'isList' => is_array($value) && Arr::isList($value),
                            'isEmpty' => empty($value)
                ];
            }

            if (is_array($value) && ! empty($value)) {
                $sub = static::dot($value, array_merge($opt, ['prepend' => "{$opt['prepend']}{$key}.", 'sub' => true]));
                $opt['res'] = array_merge($opt['res'], $sub['res']);
                if ($opt['meta']) $opt['metaData'] = array_merge($opt['metaData'], $sub['metaData']);
            } else {
                $opt['res']["{$opt['prepend']}{$key}"] = $value;
            }
        }

        return ($opt['sub'] ? $opt : ($opt['meta'] ? [$opt['res'], $opt['metaData']] : $opt['res']));
    }

    public static function flatten(iterable $array, $depth = INF) : array
    {
        $result = [];

        foreach ($array as $item) {
            if (! is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : static::flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }
}