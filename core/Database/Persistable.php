<?php

declare(strict_types=1);

namespace Core\Database;

/**
 * ISP: write operations separated from read operations.
 */
interface Persistable
{
    public function create(array $data): int;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}

