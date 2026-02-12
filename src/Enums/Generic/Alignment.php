<?php

declare(strict_types=1);

namespace Aybarsm\Support\Enums\Generic;
use Aybarsm\Support\Enums\Generic\AlignmentVertical as Vertical;
use Aybarsm\Support\Enums\Generic\AlignmentHorizontal as Horizontal;

enum Alignment: int
{
    case TOP_LEFT = Vertical::TOP->value | Horizontal::LEFT->value;
    case TOP_RIGHT = Vertical::TOP->value | Horizontal::RIGHT->value;
    case TOP_CENTRE = Vertical::TOP->value | Horizontal::CENTRE->value;
    case BOTTOM_LEFT = Vertical::BOTTOM->value | Horizontal::LEFT->value;
    case BOTTOM_RIGHT = Vertical::BOTTOM->value | Horizontal::RIGHT->value;
    case BOTTOM_CENTRE = Vertical::BOTTOM->value | Horizontal::CENTRE->value;

    case MIDDLE_LEFT = Vertical::MIDDLE->value | Horizontal::LEFT->value;
    case MIDDLE_RIGHT = Vertical::MIDDLE->value | Horizontal::RIGHT->value;
    case MIDDLE_CENTRE = Vertical::MIDDLE->value | Horizontal::CENTRE->value;

    public const Alignment TOP_CENTER = self::TOP_CENTRE;
    public const Alignment BOTTOM_CENTER = self::BOTTOM_CENTRE;
    public const Alignment MIDDLE_CENTER = self::MIDDLE_CENTRE;

    public function isTop(): bool
    {
        return flags_has($this->value, Vertical::TOP->value);
    }
    public function isBottom(): bool
    {
        return flags_has($this->value, Vertical::BOTTOM->value);
    }
    public function isMiddle(): bool
    {
        return flags_has($this->value, Vertical::MIDDLE->value);
    }
    public function isLeft(): bool
    {
        return flags_has($this->value, Horizontal::LEFT->value);
    }
    public function isRight(): bool
    {
        return flags_has($this->value, Horizontal::RIGHT->value);
    }
    public function isCentre(): bool
    {
        return flags_has($this->value, Horizontal::CENTRE->value);
    }
    public function isCenter(): bool
    {
        return $this->isCentre();
    }

    public function render(
        mixed $value,
        ?int $height = null,
        ?int $width = null,
        ?Formatter $formatter = null,
    ): string
    {
        $aligns = $this->formatter($value, $height, $width);
        if ($formatter) $value = $formatter->formatted($value);
        return $aligns->formatted($value);
    }

    public function formatter(
        mixed $value,
        ?int $height = null,
        ?int $width = null,
    ): Formatter
    {
        $vAlign = match(true){
            $this->isTop() => Vertical::TOP,
            $this->isBottom() => Vertical::BOTTOM,
            default => Vertical::MIDDLE,
        };

        $hAlign = match(true){
            $this->isLeft() => Horizontal::LEFT,
            $this->isRight() => Horizontal::RIGHT,
            default => Horizontal::CENTRE,
        };

        return new FormatterComponent(
            formatter: $vAlign->formatter($value, $height)->formatted(
                $hAlign->formatter($value, $width)->formatted('%s')
            )
        );
    }
}
