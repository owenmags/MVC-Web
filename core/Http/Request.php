<?php

declare(strict_types=1);

namespace Core\Http;

/**
 * Wraps incoming HTTP data so controllers don't touch superglobals directly.
 */
final class Request
{
    public function __construct(
        private string $method,
        private string $uri,
        private array $query,
        private array $body,
        private array $server,
    ) {
    }

    public static function capture(): self
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';

        // strip subfolder when using XAMPP, e.g. /web/WEB/public/posts -> /posts
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($base !== '' && $base !== '/' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base)) ?: '/';
        }

        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        return new self(
            method: strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            uri: $uri,
            query: $_GET,
            body: $_POST,
            server: $_SERVER,
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        // merge GET and POST - good enough for this project
        return array_merge($this->query, $this->body);
    }

    public function has(string $key): bool
    {
        return isset($this->body[$key]) || isset($this->query[$key]);
    }
}