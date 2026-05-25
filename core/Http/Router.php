<?php

declare(strict_types=1);

namespace Core\Http;

// Router uses HttpMethod constants so we don't typo GET/POST strings

/**
 * Router only resolves URIs to controller actions (SRP).
 * It does NOT dispatch or render views.
 */
final class Router
{
    /** @var array<int, array{method: string, uri: string, action: array}> */
    private array $routes = [];

    public function get(string $uri, array $action): void
    {
        $this->register(HttpMethod::GET, $uri, $action);
    }

    public function post(string $uri, array $action): void
    {
        $this->register(HttpMethod::POST, $uri, $action);
    }

    public function register(string $method, string $uri, array $action): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => $uri,
            'action' => $action,
        ];
    }

    /**
     * @return array{action: array, params: array<string, string>}|null
     */
    public function resolve(Request $request): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method()) {
                continue;
            }

            $params = $this->matchUri($route['uri'], $request->uri());
            if ($params !== null) {
                return [
                    'action' => $route['action'],
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    /**
     * @return array<string, string>|null
     */
    private function matchUri(string $pattern, string $uri): ?array
    {
        $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}

