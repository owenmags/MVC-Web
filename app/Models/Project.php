<?php

declare(strict_types=1);

namespace App\Models;

use Core\Database\ORM\Model;

/**
 * Project model - model layer para sa projects table (PDF: app/Models/).
 */
final class Project extends Model
{
    protected static string $table = 'projects';

    protected array $fillable = [
        'name',
        'description',
    ];

    /** @return list<static> */
    public static function allByName(): array
    {
        $rows = static::query()->orderBy('name', 'ASC')->get();
        return array_map(static fn (array $row) => static::newFromRow($row), $rows);
    }
}
