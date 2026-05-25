<?php

declare(strict_types=1);

namespace Core\Support;

/**
 * Helps when the app lives in a subfolder like /web/WEB/public
 */
final class BasePath
{
    private static string $url = '';

    public static function set(string $url): void
    {
        self::$url = rtrim($url, '/');
    }

    public static function get(): string
    {
        return self::$url;
    }

    public static function url(string $path = '/'): string
    {
        if ($path === '/' || $path === '') {
            return self::$url ?: '/';
        }

        return self::$url . '/' . ltrim($path, '/');
    }
}
