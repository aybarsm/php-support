<?php

declare(strict_types=1);

namespace Aybarsm\Support\Enums\Generic;

use Aybarsm\Support\Concerns\IsEnum;

enum OsFamily
{
    use IsEnum;
    case WINDOWS;
    case BSD;
    case DARWIN;
    case SOLARIS;
    case LINUX;
    case UNKNOWN;

    public static function current(): string
    {
        return mb_strtolower(PHP_OS_FAMILY);
    }
    public static function is(string|OsFamily $family): bool
    {
        return self::current() === mb_strtolower(self::make($family)->name);
    }
}
