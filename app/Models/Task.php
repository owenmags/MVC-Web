<?php

declare(strict_types=1);

namespace App\Models;

use Core\Database\ORM\Model;

/**
 * Task model - model layer para sa tasks table (PDF requirement).
 */
final class Task extends Model
{
    protected static string $table = 'tasks';

    protected array $fillable = [
        'title',
        'description',
        'project_id',
        'due_date',
        'status',
    ];

    /** @return list<array<string, mixed>> */
    public static function allWithProject(): array
    {
        $rows = [];
        foreach (self::all() as $task) {
            $rows[] = self::attachProjectName($task->toArray());
        }
        return $rows;
    }

    /** @return array<string, mixed>|null */
    public static function findWithProject(int $id): ?array
    {
        $task = self::find($id);
        if ($task === null) {
            return null;
        }
        return self::attachProjectName($task->toArray());
    }

    /** @param array<string, mixed> $row */
    private static function attachProjectName(array $row): array
    {
        if (!empty($row['project_id'])) {
            $project = Project::find((int) $row['project_id']);
            $row['project_name'] = $project?->name ?? 'N/A';
        } else {
            $row['project_name'] = 'No project';
        }
        return $row;
    }
}
