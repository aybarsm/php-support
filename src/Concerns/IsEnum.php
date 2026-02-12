<?php

declare(strict_types=1);

namespace Aybarsm\Support\Concerns;

trait IsEnum
{
    public static function make(string|int|self $value): static
    {
        if ($value instanceof self) {
            return $value;
        }

        try{
            $value = strval($value);
        }catch (\Throwable $e){
            throw new \InvalidArgumentException(
                sprintf('Unable to convert value to string for `%s`', static::class),
            );
        }

        $value = mb_strtolower(strval($value));
        foreach(self::cases() as $case) {
            if (mb_strtolower(strval($case->name)) === $value || (property_exists($case, 'value') && mb_strtolower(strval($case->value)) === $value)) {
                return $case;
            }
        }

        throw new \InvalidArgumentException(
            sprintf('Unable to convert value `%s` to `%s`', $value, static::class),
        );
    }
}
