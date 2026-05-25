<?php ob_start(); ?>
<div class="page-top">
    <h2><?= htmlspecialchars($title) ?></h2>
    <div class="page-actions">
        <a class="btn btn-outline" href="<?= ($base ?? '') ?>/tasks">View All Tasks</a>
        <a class="btn" href="<?= ($base ?? '') ?>/tasks/create">Add New Task</a>
    </div>
</div>
<div class="card">
<p><strong>Title:</strong> <?= htmlspecialchars($task['title']) ?></p>
<p><strong>Description:</strong> <?= nl2br(htmlspecialchars($task['description'] ?? '')) ?: '<em>None</em>' ?></p>
<p><strong>Project:</strong> <?= htmlspecialchars($task['project_name'] ?? 'No project') ?></p>
<p><strong>Due:</strong> <?= htmlspecialchars($task['due_date'] ?? 'Not set') ?></p>
<p><strong>Status:</strong> <?= htmlspecialchars($task['status'] ?? '') ?></p>
</div>
<p><a class="btn" href="<?= ($base ?? '') ?>/tasks/<?= (int)$task['id'] ?>/edit">Edit</a></p>
<form method="POST" action="<?= ($base ?? '') ?>/tasks/<?= (int)$task['id'] ?>/delete" onsubmit="return confirm('Delete?');"><button type="submit" class="btn btn-danger">Delete</button></form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php';
