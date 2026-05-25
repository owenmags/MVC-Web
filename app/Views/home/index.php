<?php ob_start(); ?>
<div class="page-top">
    <h2><?= htmlspecialchars($title) ?></h2>
    <div class="page-actions">
        <a class="btn btn-outline" href="<?= ($base ?? '') ?>/tasks">View All Tasks</a>
        <a class="btn" href="<?= ($base ?? '') ?>/tasks/create">Add New Task</a>
    </div>
</div>
<p>Welcome to your task dashboard.</p>
<div class="summary-grid">
    <div class="summary-box"><strong><?= (int) $total ?></strong><span>Total</span></div>
    <div class="summary-box"><strong><?= (int) $pending ?></strong><span>Pending</span></div>
    <div class="summary-box"><strong><?= (int) $done ?></strong><span>Done</span></div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layout.php';
