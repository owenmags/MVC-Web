<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Task;

final class SQLiteTaskRepository implements TaskRepositoryInterface
{
    public function findAll(): array
    {
        return Task::allWithProject();
    }

    public function allWithProject(): array
    {
        return Task::allWithProject();
    }

    public function findById(int $id): ?array
    {
        return Task::findWithProject($id);
    }

    public function create(array $data): int
    {
        $task = Task::create($data);
        return (int) $task->id;
    }

    public function update(int $id, array $data): bool
    {
        $task = Task::find($id);
        if ($task === null) {
            return false;
        }
        $task->fill($data);
        return $task->save();
    }

    public function delete(int $id): bool
    {
        $task = Task::find($id);
        return $task?->delete() ?? false;
    }
}
