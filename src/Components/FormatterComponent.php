<?php

declare(strict_types=1);

namespace Aybarsm\Support\Components;

class FormatterComponent implements namespace\Contracts\Formatter
{
    protected ?int $repeats = null;
    protected bool $isTrim = false;
    protected bool $isBlankAware = false;
    protected string $formatter = '%s';

    public function __construct(
        string $formatter = '%s',
        ?int $repeats = null,
        bool $isTrim = false,
        bool $isBlankAware = false
    ){
        $this->setFormatter($formatter);
        $this->setRepeats($repeats);
        $this->setIsTrim($isTrim);
        $this->setIsBlankAware($isBlankAware);
    }

    public static function validateFormatter(mixed $formatter, bool $throws = false): bool
    {
        $formatter = strval($formatter);
        $ret = substr_count($formatter, '%s') === 1;

        throw_if(
            $throws && $ret === false,
            \InvalidArgumentException::class,
            sprintf('FormatterComponent [%s] must exactly have 1 `%s`', $formatter, '%s')
        );

        return $ret;
    }

    public static function validateRepeats(?int $repeat, bool $throws = false): bool
    {
        $ret = $repeat === null || $repeat >= 1;
        throw_if(
            $throws && $ret === false,
            \InvalidArgumentException::class,
            'Repeat must be greater or equal 1, when it is set.',
        );

        return $ret;
    }
    public function setFormatter(mixed $formatter): static
    {
        $formatter = strval($formatter);
        static::validateFormatter($formatter, true);

        $this->formatter = $formatter;
        return $this;
    }
    public function getFormatter(): string
    {
        return $this->formatter;
    }

    public function setRepeats(?int $repeats): static
    {
        static::validateRepeats($repeats, true);
        $this->repeats = $repeats;
        return $this;
    }
    public function getRepeats(): ?int
    {
        return $this->repeats;
    }

    public function setIsTrim(bool $isTrim): static
    {
        $this->isTrim = $isTrim;
        return $this;
    }

    public function isTrim(): bool
    {
        return $this->isTrim;
    }

    public function isBlankAware(): bool
    {
        return $this->isBlankAware;
    }

    public function setIsBlankAware(bool $isBlankAware): static
    {
        $this->isBlankAware = $isBlankAware;
        return $this;
    }

    public function formatted(mixed $value): string
    {
        $value = strval($value);

        if ($this->isBlankAware() && mb_strlen($value) === 0) {
            return '';
        }

        if ($this->isTrim()) $value = trim($value);
        if ($this->getRepeats()) $value = str_repeat($value, $this->getRepeats());

        return sprintf($this->getFormatter(), $value);
    }

    public function cleanFormatting(mixed $value): string
    {
        $value = strval($value);
        $patterns = config('utx.patterns.console.style.cleanup', []);
        return preg_replace($patterns, '$2', $value);
    }
}