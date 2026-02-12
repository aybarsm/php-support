<?php

declare(strict_types=1);

namespace Aybarsm\Support;

class Arr
{
    public static function wrap(mixed $value = []): array
    {
        if (is_array($value)) {
            return $value;
        }elseif(is_iterable($value)){
            return iterator_to_array($value);
        }

        if (class_exists('Illuminate\Database\Eloquent\Model') && $value instanceof \Illuminate\Database\Eloquent\Model::class) {
            return $value->toArray();
        }

        if (class_exists('Illuminate\Support\Enumerable') && $value instanceof \Illuminate\Support\Enumerable::class) {
            return $value->all();
        }

        if (class_exists('Tempest\Support\Arr\ArrayInterface') && $value instanceof \Tempest\Support\Arr\ArrayInterface::class) {
            return $value->toArray();
        }

        if ($value === null) {
            return [];
        }

        return [$value];
    }
}