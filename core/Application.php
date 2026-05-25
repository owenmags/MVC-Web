<?php

declare(strict_types=1);

namespace Core;

use App\Repositories\TaskRepositoryInterface;
use App\Repositories\SQLiteTaskRepository;
use Core\Container\Container;
use Core\Database\Connection;
use Core\Database\ORM\Model as OrmModel;
use Core\Database\QueryBuilder;
use Core\Database\SQLiteDriver;
use Core\Http\Dispatcher;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Core\Support\BasePath;
use Core\View\Engine;
use RuntimeException;

final class Application
{
    private Container $container;
    private Router $router;

    private function __construct(
        private string $basePath,
    ) {
        $this->container = new Container();
        $this->router = new Router();
    }

    public static function create(string $basePath): self
    {
        $app = new self($basePath);
        $app->bootstrap();
        return $app;
    }

    private function bootstrap(): void
    {
        $config = require $this->basePath . '/config/app.php';
        $dbConfig = require $this->basePath . '/config/database.php';

        // base URL for XAMPP subfolders (e.g. /web/WEB/public)
        $baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($baseUrl === '/') {
            $baseUrl = '';
        }
        BasePath::set($baseUrl);

        $views = new Engine($this->basePath . '/app/Views', $baseUrl);
        $this->container->singleton(Engine::class, $views);

        // Database setup (using SQLite for easy demo)
        $driver = match ($dbConfig['driver']) {
            'mysql' => new \Core\Database\MySQLDriver(),
            default => new SQLiteDriver(),
        };

        $connectionConfig = match ($dbConfig['driver']) {
            'mysql' => $dbConfig['mysql'],
            default => $dbConfig['sqlite'],
        };

        $connection = new Connection($driver, $connectionConfig);
        $this->container->singleton(Connection::class, $connection);

        $queryBuilder = new QueryBuilder($connection->pdo());
        $this->container->singleton(QueryBuilder::class, $queryBuilder);

        // wire ORM to the query builder
        OrmModel::setConnection($queryBuilder);

        // DIP: bind interface to concrete repo
        $this->container->bind(TaskRepositoryInterface::class, SQLiteTaskRepository::class);

        $this->setupDatabase($connection);
        $this->loadRoutes();
    }

    private function setupDatabase(Connection $connection): void
    {
        $pdo = $connection->pdo();
        $pdo->exec('CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )');
        $pdo->exec('CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            project_id INTEGER,
            title TEXT NOT NULL,
            description TEXT,
            due_date TEXT,
            status TEXT NOT NULL DEFAULT "pending",
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
        )');
        $pdo->exec('INSERT OR IGNORE INTO projects (id, name, description) VALUES
            (1, "School", "School work"),
            (2, "Personal", "Personal tasks")');
    }

    private function loadRoutes(): void
    {
        $router = $this->router;
        require $this->basePath . '/routes/web.php';
    }

    public function run(): void
    {
        $request = Request::capture();
        $this->container->singleton(Request::class, $request);

        // optional logging middleware
        (new \App\Middleware\LogMiddleware())->handle($request->uri());

        $match = $this->router->resolve($request);

        if ($match === null) {
            Response::html('<h1>404 - Page not found</h1>', 404)->send();
            return;
        }

        try {
            $dispatcher = new Dispatcher($this->container);
            $result = $dispatcher->dispatch($match['action'], $match['params']);

            if ($result instanceof Response) {
                $result->send();
            } elseif (is_string($result)) {
                Response::html($result)->send();
            } else {
                throw new RuntimeException('Controller must return Response or string');
            }
        } catch (\Throwable $e) {
            $config = require $this->basePath . '/config/app.php';
            if ($config['debug']) {
                Response::html(
                    '<h1>Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>',
                    500
                )->send();
            } else {
                Response::html('<h1>500 - Something went wrong</h1>', 500)->send();
            }
        }
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function container(): Container
    {
        return $this->container;
    }
}

