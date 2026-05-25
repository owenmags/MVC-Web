<?php

declare(strict_types=1);

namespace Core\Database;

use PDO;

/**
 * OCP: add MySQLDriver or SQLiteDriver without changing Connection.php
 */
interface DatabaseDriver
{
    public function connect(array $config): PDO;
}

