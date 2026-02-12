<?php

declare(strict_types=1);

namespace Aybarsm\Support\Enums\Generic;

enum ModeMatch
{
    case ANY;
    case ALL;

    public function early(bool $result): ?bool
    {
        if ($result && $this === self::ANY) {
            return true;
        }elseif(! $result && $this === self::ALL) {
            return false;
        }

        return null;
    }

    public function final(): bool
    {
        return $this === self::ALL;
    }

}
