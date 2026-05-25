<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Support\BasePath;

/**
 * Simple HTTP response wrapper.
 */
final class Response
{
    public function __construct(
        private string $content,
        private int $status = 200,
        private array $headers = ['Content-Type' => 'text/html; charset=utf-8'],
    ) {
    }

    public static function html(string $content, int $status = 200): self
    {
        return new self($content, $status);
    }

    public static function redirect(string $path, int $status = 302): self
    {
        $url = str_starts_with($path, 'http') ? $path : BasePath::url($path);
        return new self('', $status, ['Location' => $url]);
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->content;
    }
}

