<?php

declare(strict_types=1);

namespace Aybarsm\Support\Enums\Generic;

enum AlignmentVertical: int
{
    case TOP = 2;
    case BOTTOM = 4;
    case MIDDLE = 8;

    public const int ALL =
        self::TOP->value |
        self::BOTTOM->value |
        self::MIDDLE->value;

    public function renderTop(
        mixed $value,
        ?int $height = null,
        ?Formatter $formatter = null,
    ): string
    {
        self::validateHeight($height);
        $length = self::getHeight($value, $formatter);
        $diff = $height - $length;
        $top = '';
        if ($diff <= 0) return $top;

        if ($this === self::BOTTOM) {
            $top = str_repeat(PHP_EOL, $diff);
        }elseif ($this === self::MIDDLE) {
            $topLength = intval(floor($diff / 2));
            $top = str_repeat(PHP_EOL, $topLength);
        }

        return $top;
    }

    public function renderBottom(
        mixed $value,
        ?int $height = null,
        ?Formatter $formatter = null,
    ): string
    {
        self::validateHeight($height);
        $length = self::getHeight($value, $formatter);
        $diff = $height - $length;
        $bottom = '';
        if ($diff <= 0) return $bottom;

        if ($this === self::TOP) {
            $bottom = str_repeat(PHP_EOL, $diff);
        }elseif ($this === self::MIDDLE) {
            $topLength = intval(floor($diff / 2));
            if ($diff-$topLength > 0){
                $bottom = str_repeat(PHP_EOL, $diff - $topLength);
            }
        }

        return $bottom;
    }

    public function render(
        mixed $value,
        ?int $height = null,
        ?Formatter $formatter = null,
    ): string
    {
        $top = $this->renderTop($value, $height, $formatter);
        $bottom = $this->renderBottom($value, $height, $formatter);

        if ($formatter) $value = $formatter->formatted($value);

        return "{$top}{$value}{$bottom}";
    }

    public function formatter(
        mixed $value,
        ?int $height = null,
    ): Formatter
    {
        $top = $this->renderTop($value, $height);
        $bottom = $this->renderBottom($value, $height);
        return new FormatterComponent(formatter: "{$top}%s{$bottom}");
    }
}
