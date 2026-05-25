<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Task;

final class TaskStatsRepository
{
    public function count(): int
    {
        return Task::count();
    }

    public function countByStatus(string $status): int
    {
        $n = 0;
        foreach (Task::all() as $task) {
            if (($task->status ?? '') === $status) {
                $n++;
            }
        }
        return $n;
    }
}
