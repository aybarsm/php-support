<?php

declare(strict_types=1);

namespace Aybarsm\Support;


class Generic
{
    public static function throw_if(mixed $condition, ...$parameters): void
    {
        $condition = namespace\Data::value($condition);

        if ($condition !== true) {
            return;
        }

        if (blank($parameters)) {
            throw new \RuntimeException(message: 'Undefined exception error message');
        }

        $paramValues = array_values($parameters);
        $primary = $paramValues[0];
        $isThrowable = $primary instanceof \Closure;
        $isExceptionClass = is_string($primary) && class_exists($primary) && is_subclass_of($primary, \Throwable::class);

        if (count($paramValues) === 1 && $isThrowable) {
            throw $primary;
        }elseif (count($paramValues) === 1 && $isExceptionClass) {
            throw new $primary(message: 'Undefined exception error message');
        }elseif($isExceptionClass) {
            unset($parameters[array_key_first($parameters)]);
            throw new $primary(...$parameters);
        }else {
            throw new \RuntimeException(...$parameters);
        }
    }

    public static function notationSegments(string $separator, string $delimiter = '#', string ...$parts): array
    {
        $pattern = preg_quote($separator, '/');
        if (! str_starts_with($pattern, $delimiter)) $pattern = "{$delimiter}{$pattern}";
        if (! str_ends_with($pattern, $delimiter)) $pattern .= $delimiter;

        $ret = [];
        foreach ($parts as $part) {
            array_push($ret, ...preg_split($pattern, $part, -1, PREG_SPLIT_NO_EMPTY));
        }

        return $ret;
    }

    public static function notation(string $separator, string $delimiter = '#', string ...$parts): string
    {
        return implode($separator, static::notationSegments($separator, $delimiter, ...$parts));
    }

    public static function with(mixed $value, callable $callback): mixed
    {
        return $callback(namespace\Data::value($value));
    }

    public static function tap(mixed $value, callable $callback): mixed
    {
        $value = namespace\Data::value($value);

        $callback($value);

        return $value;
    }

    public static function box(\Closure $callback): array
    {
        $err = null;

        try {
            $value = $callback();
        }catch (\Throwable $exception){
            $err = $exception;
        }

        return [$value, $err];
    }
}