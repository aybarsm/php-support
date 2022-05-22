<?php

namespace Aybarsm\Support;

class Str
{
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
}