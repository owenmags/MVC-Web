# MVC Web — Task Manager (PHP MVC Final Project)

Task Manager MVP built on a custom PHP MVC framework.

**Author:** Owen Robert S. Magsayo
**GitHub:** https://github.com/owenmags/MVC-Web
**Course:** SP Elec 2A - Web Application Development 2 (2025–2026)

---

## Requirements Met

- PHP 8.3+ (enums, `match`, readonly properties, constructor promotion)
- MVC + front controller (`public/index.php`)
- PSR-4 autoloading: `Core\` → `core/`, `App\` → `app/`
- SOLID principles (see `SOLID-JUSTIFICATION.md`)
- CRUD MVP with validation and SQLite database

---

## Setup

### Option 1 — PHP Built-in Server

1. Install PHP 8.3 or higher.
2. Install Composer dependencies:

```bash
composer install
```

3. Start the built-in server from the project root:

```bash
php -S localhost:8000 -t public
```

4. Open http://localhost:8000 in your browser.

### Option 2 — XAMPP (Apache)

1. Place the project folder inside your `htdocs` directory.
2. Make sure **mod_rewrite** is enabled in `httpd.conf`.
3. Open the app through the **public** folder:
   - `http://localhost/MVC Web/public/`
4. Do **not** open `http://localhost/` alone — always include `/public/` in the URL.

> The SQLite database file is created automatically at `storage/database.sqlite` on first run.

---

## Project Structure

MVC Web/
├── app/                # Application layer
│   ├── Controllers/
│   ├── Models/
│   ├── Repositories/
│   ├── Validation/
│   └── Views/
├── core/               # Custom MVC framework
├── config/             # App and database config
├── routes/             # Route definitions
├── public/             # Document root (entry point)
├── storage/            # SQLite database and logs
└── vendor/             # Composer dependencies (not tracked)
---

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/` | Home page |
| GET | `/tasks` | List all tasks |
| GET | `/tasks/create` | Show create form |
| POST | `/tasks` | Store new task |
| GET | `/tasks/{id}` | Show a task |
| GET | `/tasks/{id}/edit` | Show edit form |
| POST | `/tasks/{id}/update` | Update a task |
| POST | `/tasks/{id}/delete` | Delete a task |

---

## Design Decisions

- **SQLite by default** — no MySQL setup needed for local development; a MySQL driver is still included to satisfy OCP/LSP.
- **Plain PHP views** — no Twig or Blade; views are rendered by `Core\View\Engine`.
- **DI container** — uses reflection-based constructor injection; interface-to-concrete bindings are declared explicitly.
- **Active Record ORM** — `core/Database/ORM/Model.php` maps models to tables (see `App\Models\Task` and `App\Models\Project`).
- **Separate Router and Dispatcher** — routing logic is decoupled from controller invocation, following SRP.

---

## ORM Usage

```php
use App\Models\Task;
use App\Models\Project;

// Read
$tasks = Task::all();
$task  = Task::find(1);

// Create
Task::create(['title' => 'New Task', 'project_id' => 1]);

// Update
$task = Task::find(1);
$task->title = 'Updated title';
$task->save();

// Delete
$task->delete();
```

Controllers depend on repository interfaces — SQL stays out of controllers entirely.

---

## MVP Description

A minimal task manager where you can create, read, update, and delete tasks organized by projects. Forms validate task titles and project names (required field, minimum length). Invalid input shows inline error messages on the form.