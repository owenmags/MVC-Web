<?php

declare(strict_types=1);

namespace Core\Container;

use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

/**
 * Basic DI container - binds interfaces to concrete classes at runtime (DIP).
 * I learned the reflection part from class notes + some online examples.
 */
final class Container
{
    /** @var array<string, string> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $instances = [];

    public function bind(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    public function make(string $class): object
    {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        $class = $this->bindings[$class] ?? $class;

        if (!class_exists($class)) {
            throw new RuntimeException("Class not found: {$class}");
        }

        $ref = new ReflectionClass($class);
        $constructor = $ref->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $args = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                if ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                    continue;
                }
                throw new RuntimeException("Cannot resolve parameter \${$param->getName()}");
            }

            $args[] = $this->make($type->getName());
        }

        return $ref->newInstanceArgs($args);
    }
}

