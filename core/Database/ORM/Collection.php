<?php

declare(strict_types=1);

namespace Core\Database\ORM;

/**
 * Wraps a list of ORM models
 */
final class Collection
{
    /**
     * @param list<Model> $items
     */
    public function __construct(
        private array $items = [],
    ) {
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function toArray(): array
    {
        return array_map(static fn (Model $model) => $model->toArray(), $this->items);
    }

    /**
     * @return list<Model>
     */
    public function all(): array
    {
        return $this->items;
    }
}
