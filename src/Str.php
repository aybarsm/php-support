<?php

declare(strict_types=1);

namespace Aybarsm\Support;

class Str
{
    public static function lines(string $value, int $limit = -1, int $flags = 0): array
    {
        return preg_split(
            pattern: '#' . preg_quote(PHP_EOL, '/') . '#',
            subject: $value,
            limit: $limit,
            flags: $flags
        );
    }
}