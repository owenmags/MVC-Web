<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\TaskStatsRepository;
use Core\Http\Response;
use Core\View\Engine;

final class HomeController
{
    public function __construct(
        private Engine $view,
        private TaskStatsRepository $stats,
    ) {
    }

    public function index(): Response
    {
        return Response::html($this->view->render('home/index', [
            'title' => 'Task Manager',
            'total' => $this->stats->count(),
            'pending' => $this->stats->countByStatus('pending'),
            'done' => $this->stats->countByStatus('done'),
        ]));
    }
}
