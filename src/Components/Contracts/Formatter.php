<?php

declare(strict_types=1);

namespace Aybarsm\Support\Components\Contracts;

interface Formatter
{
    public static function validateFormatter(mixed $formatter, bool $throws = false): bool;
    public static function validateRepeats(?int $repeat, bool $throws = false): bool;
    public function formatted(mixed $value): string;
    public function cleanFormatting(mixed $value): string;
    public function setFormatter(mixed $formatter): static;
    public function getFormatter(): string;
    public function setRepeats(?int $repeats): static;
    public function getRepeats(): ?int;
    public function setIsTrim(bool $isTrim): static;
    public function isTrim(): bool;
    public function setIsBlankAware(bool $isBlankAware): static;
    public function isBlankAware(): bool;
}