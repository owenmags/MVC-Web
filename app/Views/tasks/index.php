<?php ob_start(); ?>
<div class="page-top">
    <h2><?= htmlspecialchars($title) ?></h2>
    <div class="page-actions">
        <a class="btn btn-outline" href="<?= ($base ?? '') ?>/tasks">View All Tasks</a>
        <a class="btn" href="<?= ($base ?? '') ?>/tasks/create">Add New Task</a>
    </div>
</div>
<?php if (empty($tasks)): ?>
    <p class="empty">No tasks yet. <a href="<?= ($base ?? '') ?>/tasks/create">Add your first task</a></p>
<?php else: ?>
<table>
<thead><tr><th>ID</th><th>Title</th><th>Project</th><th>Due</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($tasks as $task): ?>
<tr>
<td><?= (int) $task['id'] ?></td>
<td><?= htmlspecialchars($task['title']) ?></td>
<td><?= htmlspecialchars($task['project_name'] ?? 'No project') ?></td>
<td><?= htmlspecialchars($task['due_date'] ?? '—') ?></td>
<td><span class="status-<?= htmlspecialchars($task['status'] ?? '') ?>"><?= htmlspecialchars($task['status'] ?? '') ?></span></td>
<td>
<a href="<?= ($base ?? '') ?>/tasks/<?= (int) $task['id'] ?>">View</a> |
<a href="<?= ($base ?? '') ?>/tasks/<?= (int) $task['id'] ?>/edit">Edit</a> |
<form method="POST" action="<?= ($base ?? '') ?>/tasks/<?= (int) $task['id'] ?>/delete" class="inline-delete" onsubmit="return confirm('Delete?');">
<button type="submit" class="btn-link">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php';
