<?php

declare(strict_types=1);

namespace Aybarsm\Support;

use Aybarsm\Support\Enums\Generic\OsFamily;

class Os
{
    public static function name(): string
    {
        return mb_strtolower(PHP_OS);
    }

    public static function family(): string
    {
        return OsFamily::current();
    }

    public static function isFamily(string|OsFamily $family): bool
    {
        return OsFamily::is($family);
    }
}