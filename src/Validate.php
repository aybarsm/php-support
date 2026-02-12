<?php

declare(strict_types=1);

namespace Aybarsm\Support;

class Validate
{
    public static function blank(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if (is_iterable($value)) {
            return count(iterator_to_array($value)) === 0;
        }

        if ($value instanceof \Countable) {
            return $value->count() === 0;
        }

        if ($value instanceof \Stringable::class) {
            return trim($value->__toString()) === '';
        }

        if (class_exists('Illuminate\Database\Eloquent\Model') && $value instanceof \Illuminate\Database\Eloquent\Model::class) {
            return false;
        }

        if (class_exists('Illuminate\Support\Enumerable') && $value instanceof \Illuminate\Support\Enumerable::class) {
            return count($value->all()) === 0;
        }

        if (class_exists('Tempest\Support\Arr\ArrayInterface') && $value instanceof \Tempest\Support\Arr\ArrayInterface::class) {
            return count($value->toArray()) === 0;
        }

        return empty($value);
    }

    public static function filled(mixed $value): bool
    {
        return ! static::blank($value);
    }
}