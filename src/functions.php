<?php

declare(strict_types=1);

namespace Aybarsm\Support {
    use Aybarsm\Support;

    function throw_if(mixed $condition, ...$parameters): void
    {
        Support\Generic::throw_if($condition, ...$parameters);
    }
    function value(mixed $value, mixed ...$args): mixed
    {
        return Support\Data::value($value, ...$args);
    }

    function blank(mixed $value): bool
    {
        return Support\Validate::blank($value);
    }

    function filled(mixed $value): bool
    {
        return Support\Validate::filled($value);
    }

    function data_path(mixed ...$parts): string
    {
        return Support\Data::path(...$parts);
    }

    function fs_path(mixed ...$parts): string
    {
        return Support\Fs::path(...$parts);
    }

    function array_wrap(mixed $value = []): array
    {
        return Support\Arr::wrap($value);
    }

    function str_lines(string $value, int $limit = -1, int $flags = 0): array
    {
        return Support\Str::lines($value, $limit, $flags);
    }

    function with(mixed $value, callable $callback): mixed
    {
        return Support\Generic::with($value, $callback);
    }

    function tap(mixed $value, callable $callback): mixed
    {
        return Support\Generic::tap($value, $callback);
    }

    function box(callable $callback): array
    {
        return Support\Generic::box($callback);
    }
}