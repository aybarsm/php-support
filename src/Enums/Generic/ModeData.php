<?php

declare(strict_types=1);

namespace Aybarsm\Support\Enums\Generic;

use Aybarsm\Support\Concerns\IsEnum;

enum ModeData: int
{
    use IsEnum;
    case UNSET = 1;
    case STRING = 2;
    case INTEGER = 4;
    case FLOAT = 8;
    case BOOLEAN = 16;
    case ITERABLE = 32;
    case OBJECT = 64;
    case NULL = 128;

    public const int ANY =
        self::STRING->value |
        self::INTEGER->value |
        self::FLOAT->value |
        self::BOOLEAN->value |
        self::ITERABLE->value |
        self::OBJECT->value |
        self::NULL->value;

    public function isValid(mixed $value): bool
    {
        return match ($this) {
            self::STRING => is_string($value),
            self::INTEGER => is_int($value),
            self::FLOAT => is_float($value),
            self::BOOLEAN => is_bool($value),
            self::ITERABLE => is_iterable($value),
            self::OBJECT => is_object($value),
            self::NULL => is_null($value),
            default => true
        };
    }
}
