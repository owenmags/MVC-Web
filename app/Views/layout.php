<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Task Manager') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars(($base ?? '') . '/css/style.css') ?>">
</head>
<body>
    <div class="wrap">
        <header class="site-header">
            <h1>Task Manager</h1>
            <nav class="site-nav">
                <a href="<?= ($base ?? '') ?>/">Home</a>
                <a href="<?= ($base ?? '') ?>/tasks">All Tasks</a>
            </nav>
        </header>
        <?= $content ?? '' ?>
        <footer class="site-footer">PHP MVC Framework — Final Examination Project</footer>
    </div>
</body>
</html>
