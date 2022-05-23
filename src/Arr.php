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

    public static function has(array $array, string|array $keys) : bool
    {
        $keys = (array) $keys;

        if (! $array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    public static function hasAny(array $array, string|array $keys) : bool
    {
        $keys = (array) $keys;

        if (! $array) {
            return false;
        }

        if ($keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            if (static::has($array, $key)) {
                return true;
            }
        }

        return false;
    }

    public static function first(array $array, callable $callback = null, $default = null) : mixed
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return value($default);
            }

            foreach ($array as $item) {
                return $item;
            }
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return value($default);
    }

    public static function last(array $array, callable $callback = null, $default = null) : mixed
    {
        if (is_null($callback)) {
            return empty($array) ? value($default) : end($array);
        }

        return static::first(array_reverse($array, true), $callback, $default);
    }

    public static function forget(array &$array, array|string|int|float $keys) : void
    {
        $original = &$array;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && static::accessible($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    public static function crossJoin(iterable ...$arrays) : array
    {
        $results = [[]];

        foreach ($arrays as $index => $array) {
            $append = [];

            foreach ($results as $product) {
                foreach ($array as $item) {
                    $product[$index] = $item;

                    $append[] = $product;
                }
            }

            $results = $append;
        }

        return $results;
    }

    public static function divide(array $array) : array
    {
        return [array_keys($array), array_values($array)];
    }

    public static function except(array $array, array|string|int|float $keys) : array
    {
        static::forget($array, $keys);

        return $array;
    }

    public static function join(array $array, string $glue, string $finalGlue = '') : string
    {
        if ($finalGlue === '') {
            return implode($glue, $array);
        }

        if (count($array) === 0) {
            return '';
        }

        if (count($array) === 1) {
            return end($array);
        }

        $finalItem = array_pop($array);

        return implode($glue, $array).$finalGlue.$finalItem;
    }

    public static function only(array $array, array|string $keys) : array
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    public static function map(array $array, callable $callback) : array
    {
        $keys = array_keys($array);

        $items = array_map($callback, $array, $keys);

        return array_combine($keys, $items);
    }

    public static function prepend(array $array, mixed $value, mixed $key = null) : array
    {
        if (func_num_args() == 2) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }

        return $array;
    }

    public static function pull(array &$array, string $key, mixed $default = null) : mixed
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    public static function query(array $array) : string
    {
        return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
    }

    public static function random(array $array, int|null $number = null, bool $preserveKeys = false) : mixed
    {
        $requested = is_null($number) ? 1 : $number;

        $count = count($array);

        if ($requested > $count) {
            throw new \InvalidArgumentException(
                "You requested {$requested} items, but there are only {$count} items available."
            );
        }

        if (is_null($number)) {
            return $array[array_rand($array)];
        }

        if ((int) $number === 0) {
            return [];
        }

        $keys = array_rand($array, $number);

        $results = [];

        if ($preserveKeys) {
            foreach ((array) $keys as $key) {
                $results[$key] = $array[$key];
            }
        } else {
            foreach ((array) $keys as $key) {
                $results[] = $array[$key];
            }
        }

        return $results;
    }

    public static function shuffle(array $array, $seed = null) : array
    {
        if (is_null($seed)) {
            shuffle($array);
        } else {
            mt_srand($seed);
            shuffle($array);
            mt_srand();
        }

        return $array;
    }

    public static function sortRecursive(array $array, int $options = SORT_REGULAR, bool $descending = false) : array
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = static::sortRecursive($value, $options, $descending);
            }
        }

        if (static::isAssoc($array)) {
            $descending
                ? krsort($array, $options)
                : ksort($array, $options);
        } else {
            $descending
                ? rsort($array, $options)
                : sort($array, $options);
        }

        return $array;
    }

    public static function toCssClasses(array $array) : string
    {
        $classList = static::wrap($array);

        $classes = [];

        foreach ($classList as $class => $constraint) {
            if (is_numeric($class)) {
                $classes[] = $constraint;
            } elseif ($constraint) {
                $classes[] = $class;
            }
        }

        return implode(' ', $classes);
    }

    public static function where(array $array, callable $callback) : array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    public static function whereNotNull(array $array) : array
    {
        return static::where($array, function ($value) {
            return ! is_null($value);
        });
    }

    public static function wrap(mixed $value) : array
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }
}