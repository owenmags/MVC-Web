<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Project;
use App\Repositories\TaskRepositoryInterface;
use App\Validation\Validator;
use Core\Http\Request;
use Core\Http\Response;
use Core\View\Engine;

final class TaskController
{
    private const STATUSES = ['pending', 'in_progress', 'done'];

    public function __construct(
        private TaskRepositoryInterface $tasks,
        private Engine $view,
        private Request $request,
    ) {
    }

    public function index(): Response
    {
        return Response::html($this->view->render('tasks/index', [
            'title' => 'All Tasks',
            'tasks' => $this->tasks->allWithProject(),
        ]));
    }

    public function create(): Response
    {
        return Response::html($this->view->render('tasks/create', [
            'title' => 'New Task',
            'projects' => $this->projectOptions(),
            'errors' => [],
            'old' => [],
        ]));
    }

    public function store(): Response
    {
        $data = $this->request->all();
        $errors = $this->validateTask($data);

        if ($errors !== []) {
            return Response::html($this->view->render('tasks/create', [
                'title' => 'New Task',
                'projects' => $this->projectOptions(),
                'errors' => $errors,
                'old' => $data,
            ]), 422);
        }

        $this->tasks->create($this->normalizeTaskData($data));
        return Response::redirect('/tasks');
    }

    public function show(string $id): Response
    {
        $task = $this->tasks->findById((int) $id);
        if ($task === null) {
            return Response::html('<h1>Task not found</h1>', 404);
        }

        return Response::html($this->view->render('tasks/show', [
            'title' => 'Task Details',
            'task' => $task,
        ]));
    }

    public function edit(string $id): Response
    {
        $task = $this->tasks->findById((int) $id);
        if ($task === null) {
            return Response::html('<h1>Task not found</h1>', 404);
        }

        return Response::html($this->view->render('tasks/edit', [
            'title' => 'Edit Task',
            'task' => $task,
            'projects' => $this->projectOptions(),
            'errors' => [],
        ]));
    }

    public function update(string $id): Response
    {
        $data = $this->request->all();
        $errors = $this->validateTask($data);

        if ($errors !== []) {
            $task = $this->tasks->findById((int) $id);
            return Response::html($this->view->render('tasks/edit', [
                'title' => 'Edit Task',
                'task' => array_merge($task ?? [], $data),
                'projects' => $this->projectOptions(),
                'errors' => $errors,
            ]), 422);
        }

        $this->tasks->update((int) $id, $this->normalizeTaskData($data));
        return Response::redirect('/tasks/' . $id);
    }

    public function destroy(string $id): Response
    {
        $this->tasks->delete((int) $id);
        return Response::redirect('/tasks');
    }

    /** @return list<array<string, mixed>> */
    private function projectOptions(): array
    {
        return array_map(static fn (Project $p) => $p->toArray(), Project::allByName());
    }

    /** @return array<string, list<string>> */
    private function validateTask(array $data): array
    {
        $validator = new Validator();
        $errors = [];

        if (!$validator->validate($data, ['title' => 'required|min:1|max:150'])) {
            $errors = $validator->errors();
        }

        $status = (string) ($data['status'] ?? 'pending');
        if (!in_array($status, self::STATUSES, true)) {
            $errors['status'][] = 'Invalid status.';
        }

        $due = trim((string) ($data['due_date'] ?? ''));
        if ($due !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $due)) {
            $errors['due_date'][] = 'Due date must be YYYY-MM-DD.';
        }

        return $errors;
    }

    /** @return array<string, mixed> */
    private function normalizeTaskData(array $data): array
    {
        $projectId = $data['project_id'] ?? '';
        return [
            'title' => trim((string) ($data['title'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'project_id' => $projectId === '' ? null : (int) $projectId,
            'due_date' => trim((string) ($data['due_date'] ?? '')) ?: null,
            'status' => (string) ($data['status'] ?? 'pending'),
        ];
    }
}
