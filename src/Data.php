<?php

declare(strict_types=1);

namespace Aybarsm\Support;
class Data
{
    public static function value(mixed $value, mixed ...$args): mixed
    {
        return $value instanceof \Closure ? $value(...$args) : $value;
    }

    public static function path(mixed ...$parts): string
    {
        $parts = array_map(static fn ($item) => strval($item), $parts);
        return namespace\Generic::notation('.', '#', ...$parts);
    }
}