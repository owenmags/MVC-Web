<?php

declare(strict_types=1);

namespace Core\Database;

use PDO;

/**
 * Connection picks a driver - we never edit this file when adding new drivers (OCP).
 */
final class Connection
{
    private PDO $pdo;

    public function __construct(DatabaseDriver $driver, array $config)
    {
        $this->pdo = $driver->connect($config);
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}

