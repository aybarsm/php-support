<?php

declare(strict_types=1);

namespace Aybarsm\Support\Components;
class StrComponent implements namespace\Contracts\Str
{
    protected string $value;
    public function __toString(): string
    {
        return $this->value;
    }
}