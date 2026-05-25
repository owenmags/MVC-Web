<?php

declare(strict_types=1);

namespace Core\Http;

/**
 * HTTP methods as constants (enum-style, works on PHP 8.0+).
 * On PHP 8.3+ you could use a backed enum instead.
 */
final class HttpMethod
{
    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';
}

