<?php

declare(strict_types=1);

namespace Core\Database;

/**
 * ISP: read-only repos only need find methods, not save/delete.
 */
interface Findable
{
    public function findAll(): array;

    public function findById(int $id): ?array;
}

