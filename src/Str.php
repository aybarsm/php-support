<?php

namespace Aybarsm\Support;

class Str
{
    protected static array $snakeCache = [];
    protected static array $camelCache = [];
    protected static array $studlyCache = [];

    public static function after(string $subject, string $search) : string
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }

    public static function afterLast(string $subject, string $search) : string
    {
        if ($search === '') {
            return $subject;
        }

        $position = strrpos($subject, (string) $search);

        if ($position === false) {
            return $subject;
        }

        return substr($subject, $position + strlen($search));
    }

    public static function before(string $subject, string $search) : string
    {
        if ($search === '') {
            return $subject;
        }

        $result = strstr($subject, (string) $search, true);

        return $result === false ? $subject : $result;
    }

    public static function beforeLast(string $subject, string $search) : string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = mb_strrpos($subject, $search);

        if ($pos === false) {
            return $subject;
        }

        return static::substr($subject, 0, $pos);
    }

    public static function substr(string $string, int $start, int|null $length = null) : string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    public static function between(string $subject, string $from, string $to) : string
    {
        if ($from === '' || $to === '') {
            return $subject;
        }

        return static::beforeLast(static::after($subject, $from), $to);
    }

    public static function betweenFirst(string $subject, string $from, string $to) : string
    {
        if ($from === '' || $to === '') {
            return $subject;
        }

        return static::before(static::after($subject, $from), $to);
    }

    public static function contains(string $haystack, string|array $needles, bool $ignoreCase = false) : bool
    {
        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
            $needles = array_map('mb_strtolower', (array) $needles);
        }

        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function containsAll(string $haystack, array $needles, bool $ignoreCase = false) : bool
    {
        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
            $needles = array_map('mb_strtolower', $needles);
        }

        foreach ($needles as $needle) {
            if (! static::contains($haystack, $needle)) {
                return false;
            }
        }

        return true;
    }

    public static function startsWith(string $haystack, string|array $needles) : bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function endsWith(string $haystack, string|array $needles) : bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function limit(string $value, int $limit = 100, string $end = '...') : string
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')).$end;
    }

    public static function remove(string|array $search, string $subject, bool $caseSensitive = true) : string
    {
        return $caseSensitive
            ? str_replace($search, '', $subject)
            : str_ireplace($search, '', $subject);
    }

    public static function random(int $length = 16) : string
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    public static function uuid() : string
    {
        $data = random_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function isUuid(string $value) : bool
    {
        // v4
        return preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $value) === 1;
    }

    public static function camel(string $value) : string
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = lcfirst(static::studly($value));
    }

    public static function finish(string $value, string $cap) : string
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:'.$quoted.')+$/u', '', $value).$cap;
    }

    public static function is(string|array $pattern, string $value) : bool
    {
        $patterns = Arr::wrap($pattern);

        $value = (string) $value;

        if (empty($patterns)) {
            return false;
        }

        foreach ($patterns as $pattern) {
            $pattern = (string) $pattern;

            if ($pattern === $value) return true;

            $pattern = preg_quote($pattern, '#');

            $pattern = str_replace('\*', '.*', $pattern);

            if (preg_match('#^'.$pattern.'\z#u', $value) === 1) return true;

        }

        return false;
    }

    public static function kebab(string $value) : string
    {
        return static::snake($value, '-');
    }

    public static function length(string $value, ?string $encoding = null) : int
    {
        if ($encoding) {
            return mb_strlen($value, $encoding);
        }

        return mb_strlen($value);
    }

    public static function lower(string $value) : string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    public static function words(string $value, int $words = 100, string $end = '...') : string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

        if (! isset($matches[0]) || static::length($value) === static::length($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]).$end;
    }

    public static function mask(string $string, string $character, int $index, ?int $length = null, string $encoding = 'UTF-8') : string
    {
        if ($character === '') return $string;


        $segment = mb_substr($string, $index, $length, $encoding);

        if ($segment === '') return $string;

        $strlen = mb_strlen($string, $encoding);
        $startIndex = $index;

        if ($index < 0) $startIndex = $index < -$strlen ? 0 : $strlen + $index;

        $start = mb_substr($string, 0, $startIndex, $encoding);
        $segmentLen = mb_strlen($segment, $encoding);
        $end = mb_substr($string, $startIndex + $segmentLen);

        return $start.str_repeat(mb_substr($character, 0, 1, $encoding), $segmentLen).$end;
    }

    public static function match(string $pattern, string $subject) : string
    {
        preg_match($pattern, $subject, $matches);

        if (! $matches) return '';

        return $matches[1] ?? $matches[0];
    }

    public static function padBoth(string $value, int $length, string $pad = ' ') : string
    {
        return str_pad($value, strlen($value) - mb_strlen($value) + $length, $pad, STR_PAD_BOTH);
    }

    public static function padLeft(string $value, int $length, string $pad = ' ') : string
    {
        return str_pad($value, strlen($value) - mb_strlen($value) + $length, $pad, STR_PAD_LEFT);
    }

    public static function padRight(string $value, int $length, string $pad = ' ') : string
    {
        return str_pad($value, strlen($value) - mb_strlen($value) + $length, $pad, STR_PAD_RIGHT);
    }

    public static function parseCallback(string $callback, ?string $default = null) : array
    {
        return static::contains($callback, '@') ? explode('@', $callback, 2) : [$callback, $default];
    }

    public static function repeat(string $string, int $times) : string
    {
        return str_repeat($string, $times);
    }

    public static function replaceArray(string $search, array $replace, string $subject) : string
    {
        $segments = explode($search, $subject);

        $result = array_shift($segments);

        foreach ($segments as $segment) {
            $result .= (array_shift($replace) ?? $search).$segment;
        }

        return $result;
    }

    public static function replace(string|array $search, string|array $replace, string|array $subject) : string
    {
        return str_replace($search, $replace, $subject);
    }

    public static function replaceFirst(string $search, string $replace, string $subject) : string
    {
        $search = (string) $search;

        if ($search === '') {
            return $subject;
        }

        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    public static function replaceLast(string $search, string $replace, string $subject) : string
    {
        if ($search === '') {
            return $subject;
        }

        $position = strrpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    public static function reverse(string $value) : string
    {
        return implode(array_reverse(mb_str_split($value)));
    }

    public static function start(string $value, string $prefix) : string
    {
        $quoted = preg_quote($prefix, '/');

        return $prefix.preg_replace('/^(?:'.$quoted.')+/u', '', $value);
    }

    public static function upper(string $value) : string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    public static function title(string $value) : string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    public static function headline(string $value) : string
    {
        $parts = explode(' ', $value);

        $parts = count($parts) > 1
            ? array_map([static::class, 'title'], $parts)
            : array_map([static::class, 'title'], static::ucsplit(implode('_', $parts)));

        $collapsed = static::replace(['-', '_', ' '], '_', implode('_', $parts));

        return implode(' ', array_filter(explode('_', $collapsed)));
    }

    public static function snake(string $value, string $delimiter = '_')
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    public static function squish(string $value) : string
    {
        return preg_replace('~(\s|\x{3164})+~u', ' ', preg_replace('~^[\s﻿]+|[\s﻿]+$~u', '', $value));
    }

    public static function studly(string $value) : string
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $words = explode(' ', static::replace(['-', '_'], ' ', $value));

        $studlyWords = array_map(fn ($word) => static::ucfirst($word), $words);

        return static::$studlyCache[$key] = implode($studlyWords);
    }

    public static function substrCount(string $haystack, string $needle, int $offset = 0, ?int $length = null) : int
    {
        if (! is_null($length)) {
            return substr_count($haystack, $needle, $offset, $length);
        } else {
            return substr_count($haystack, $needle, $offset);
        }
    }

    public static function substrReplace(string|array $string, string|array $replace, array|int $offset = 0, array|int|null $length = null) : string|array
    {
        if ($length === null) {
            $length = strlen($string);
        }

        return substr_replace($string, $replace, $offset, $length);
    }

    public static function swap(array $map, string $subject) : string
    {
        return strtr($subject, $map);
    }

    public static function lcfirst(string $string) : string
    {
        return static::lower(static::substr($string, 0, 1)).static::substr($string, 1);
    }

    public static function ucfirst(string $string) : string
    {
        return static::upper(static::substr($string, 0, 1)).static::substr($string, 1);
    }

    public static function ucsplit(string $string) : array
    {
        return preg_split('/(?=\p{Lu})/u', $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    public static function wordCount(string $string, ?string $characters = null) : int
    {
        return str_word_count($string, 0, $characters);
    }

    public static function flushCache() : void
    {
        static::$snakeCache = [];
        static::$camelCache = [];
        static::$studlyCache = [];
    }
}