<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Container\Container;

/**
 * Takes a resolved route and calls the controller method.
 * Separate from Router on purpose (Single Responsibility).
 */
final class Dispatcher
{
    public function __construct(
        private Container $container,
    ) {
    }

    public function dispatch(array $action, array $params): mixed
    {
        [$controllerClass, $method] = $action;

        $controller = $this->container->make($controllerClass);

        return $controller->{$method}(...array_values($params));
    }
}

