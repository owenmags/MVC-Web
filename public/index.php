<?php

declare(strict_types=1);

// only place we manually require something (allowed by the rubric)
require dirname(__DIR__) . '/vendor/autoload.php';

use Core\Application;

$app = Application::create(dirname(__DIR__));
$app->run();
