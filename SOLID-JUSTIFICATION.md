# SOLID Design Justification

This document explains how each SOLID principle appears in my PHP MVC framework project.

## S — Single Responsibility Principle

Each class has one main job:

- `Core\Http\Router` — only registers and resolves routes (`register()`, `resolve()`). It does not call controllers or render HTML.
- `Core\Http\Dispatcher` — only invokes the controller method after routing.
- `Core\Http\Request` — only wraps `$_GET`, `$_POST`, and the URI.
- `Core\Http\Response` — only wraps status, headers, and body output.
- `App\Models\Post` — data access helper; it does not render views.
- `App\Controllers\PostController` — handles HTTP actions but does not build raw SQL (that stays in repositories).

## O — Open/Closed Principle

The database layer is open for extension without modifying `Connection.php`:

- `Core\Database\DatabaseDriver` interface defines `connect(array $config): PDO`.
- `MySQLDriver` and `SQLiteDriver` both implement it.
- To support a new database, I add a new driver class instead of editing `Connection.php`.

## L — Liskov Substitution Principle

`MySQLDriver` and `SQLiteDriver` are interchangeable wherever a `DatabaseDriver` is expected. Both return a working `PDO` instance with the same method signature and no unexpected behaviour.

Similarly, `SQLitePostRepository` and `MySQLPostRepository` both implement `PostRepositoryInterface` with the same methods, so either can be bound in the container.

## I — Interface Segregation Principle

Instead of one large “repository” interface with unused methods:

- `Core\Database\Findable` — read-only: `findAll()`, `findById()`.
- `Core\Database\Persistable` — write: `create()`, `update()`, `delete()`.

`PostStatsRepository` implements only `Findable` because it only counts/reads posts. It is never forced to implement empty `save()` or `delete()` stubs.

## D — Dependency Inversion Principle

High-level code depends on abstractions:

- `PostController` type-hints `PostRepositoryInterface`, not `SQLitePostRepository`.
- `Connection` depends on `DatabaseDriver`, not a specific driver class.

The DI container (`Core\Container\Container`) binds abstractions at runtime in `Application::bootstrap()`:

```php
$this->container->bind(PostRepositoryInterface::class, SQLitePostRepository::class);
```

Controllers are resolved with constructor injection via reflection in `Container::make()`.

## ORM Layer (Active Record)

The framework includes a simple ORM in `core/Database/ORM/`:

- `ORM\Model` — base Active Record (`find`, `all`, `create`, `save`, `delete`)
- `ORM\Collection` — wraps multiple model instances
- `App\Models\Post` — application model with `$table` and `$fillable`

Repositories (`SQLitePostRepository`) call the ORM internally, so controllers stay decoupled while the database is accessed through model objects instead of raw SQL in controllers.
