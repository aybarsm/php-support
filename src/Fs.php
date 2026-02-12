<?php

declare(strict_types=1);

namespace Aybarsm\Support;

class Fs
{
    protected static array $meta = [];

    public static function pathParts(array $parts): array
    {
        return array_values(array_map(static fn ($part) => strval($part), $parts));
    }

    public static function pathSegments(array $parts): array
    {
        $parts = static::pathParts($parts);
        return namespace\Generic::notationSegments(DIRECTORY_SEPARATOR, '#', ...$parts);
    }

    public static function join(mixed ...$parts): string
    {
        return implode(DIRECTORY_SEPARATOR, static::pathSegments($parts));
    }

    public static function resolvePathPart(string $part): string
    {
        return match ($part) {
            '.', ':current', ':cwd' => realpath(getcwd()),
            '~', ':home' => realpath($_SERVER['HOME']),
            ':tmp', ':temp' => realpath(sys_get_temp_dir()),
            ':root' => static::root(),
            ':vendor' => static::vendor(),
            default => $part,
        };
    }

    public static function path(mixed ...$parts): ?string
    {
        if (count($parts) === 0) {
            return static::root();
        }

        $parts = array_map(
            static fn (string $part) => static::resolvePathPart($part),
            static::pathSegments(static::pathParts($parts))
        );

        return static::join(...$parts);
    }


    public static function vendor(mixed ...$parts): string
    {
        if (!isset(static::$meta['path.vendor'])) {
            $dir = dirname(realpath($_SERVER['PHP_SELF']));
            while(true){
                $vendor = $dir . DIRECTORY_SEPARATOR . 'vendor';
                if (file_exists($vendor) && is_dir($vendor)){
                    static::$meta['path.vendor'] = $vendor;
                    break;
                }
                $dir = dirname($dir);
            }
        }

        return count($parts) === 0 ? static::$meta['path.vendor'] : static::join(static::$meta['path.vendor'], ...$parts);
    }

    public static function root(mixed ...$parts): string
    {
        if (!isset(static::$meta['path.root'])) {
            static::$meta['path.root'] = dirname(static::vendor());
        }

        return count($parts) === 0 ? static::$meta['path.root'] : static::join(static::$meta['path.root'], ...$parts);
    }
}