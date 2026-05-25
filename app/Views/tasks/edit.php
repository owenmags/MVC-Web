<?php ob_start(); ?>
<div class="page-top">
    <h2><?= htmlspecialchars($title) ?></h2>
    <div class="page-actions">
        <a class="btn btn-outline" href="<?= ($base ?? '') ?>/tasks">View All Tasks</a>
        <a class="btn" href="<?= ($base ?? '') ?>/tasks/create">Add New Task</a>
    </div>
</div>
<?php if (!empty($errors)): ?><div class="errors"><ul><?php foreach ($errors as $fe) { foreach ($fe as $m) echo '<li>'.htmlspecialchars($m).'</li>'; } ?></ul></div><?php endif; ?>
<form method="POST" action="<?= ($base ?? '') ?>/tasks/<?= (int)$task['id'] ?>/update">
<label>Title *</label><input type="text" name="title" value="<?= htmlspecialchars($task['title'] ?? '') ?>" required>
<label>Description</label><textarea name="description"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
<label>Project</label><select name="project_id"><option value="">— No project —</option><?php foreach ($projects as $p): ?><option value="<?= (int)$p['id'] ?>" <?= (($task['project_id']??'')==$p['id'])?'selected':'' ?>><?= htmlspecialchars($p['name']) ?></option><?php endforeach; ?></select>
<label>Due Date</label><input type="date" name="due_date" value="<?= htmlspecialchars($task['due_date'] ?? '') ?>">
<label>Status</label><select name="status"><?php foreach (['pending','in_progress','done'] as $s): ?><option value="<?= $s ?>" <?= ($task['status']??'')===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select>
<p style="margin-top:16px"><button type="submit" class="btn">Update</button> <a href="<?= ($base ?? '') ?>/tasks/<?= (int)$task['id'] ?>">Cancel</a></p>
</form>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php';
