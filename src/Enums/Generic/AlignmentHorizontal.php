<?php

declare(strict_types=1);

namespace Aybarsm\Support\Enums\Generic;

enum AlignmentHorizontal: int
{
    case LEFT = 16;
    case RIGHT = 32;
    case CENTRE = 64;

    public const AlignmentHorizontal CENTER = self::CENTRE;
    public const int ALL =
        self::LEFT->value |
        self::RIGHT->value |
        self::CENTRE->value;
    public function renderLeft(
        mixed $value,
        ?int $width = null,
        ?Formatter $formatter = null,
    ): string
    {
        self::validateWidth($width, true);
        $value = self::getBaseValue($value, $formatter);
        $length = self::getWidth($value, $formatter);
        $diff = $width - $length;
        $left = '';
        if ($diff <= 0) return $left;

        if ($this === self::RIGHT) {
            $left = str_repeat(' ', $diff);
        }elseif ($this === self::CENTRE) {
            $leftLength = intval(floor($diff / 2));
            $left = str_repeat(' ', $leftLength);
        }

        return $left;
    }

    public function renderRight(
        mixed $value,
        ?int $width = null,
        ?Formatter $formatter = null,
    ): string
    {
        self::validateWidth($width, true);
        $value = self::getBaseValue($value, $formatter);
        $length = self::getWidth($value, $formatter);
        $diff = $width - $length;
        $right = '';
        if ($diff <= 0) return $right;

        if ($this === self::LEFT) {
            $right = str_repeat(' ', $diff);
        }elseif ($this === self::CENTRE) {
            $leftLength = intval(floor($diff / 2));
            if ($diff-$leftLength > 0){
                $right = str_repeat(' ', $diff - $leftLength);
            }
        }

        return $right;
    }
    public function render(
        mixed $value,
        ?int $width = null,
        ?Formatter $formatter = null,
    ): string
    {
        $left = $this->renderLeft($value, $width, $formatter);
        $right = $this->renderRight($value, $width, $formatter);

        if ($formatter) $value = $formatter->formatted($value);

        return "{$left}{$value}{$right}";
    }

    public function formatter(
        mixed $value,
        ?int $width = null,
    ): Formatter
    {
        $left = $this->renderLeft($value, $width);
        $right = $this->renderRight($value, $width);
        return new FormatterComponent(formatter: "{$left}%s{$right}");
    }
}
