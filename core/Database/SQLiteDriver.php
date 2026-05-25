<?php

declare(strict_types=1);

namespace Core\Database;

use PDO;

final class SQLiteDriver implements DatabaseDriver
{
    public function connect(array $config): PDO
    {
        $path = $config['path'];
        $dir = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}

