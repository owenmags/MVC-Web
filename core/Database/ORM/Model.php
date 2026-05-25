<?php

declare(strict_types=1);

namespace Core\Database\ORM;

use Core\Database\QueryBuilder;
use RuntimeException;

/**
 * Simple Active Record ORM.
 * Each model maps to one database table (SRP - one model, one table).
 *
 * Example:
 *   $post = Post::find(1);
 *   $post->title = 'Updated';
 *   $post->save();
 */
abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    /** @var list<string> columns allowed for mass assignment */
    protected array $fillable = [];

    /** @var array<string, mixed> current row data */
    protected array $attributes = [];

    /** true when loaded from DB or after insert */
    protected bool $exists = false;

    private static ?QueryBuilder $db = null;

    public static function setConnection(QueryBuilder $db): void
    {
        self::$db = $db;
    }

    protected static function query(): QueryBuilder
    {
        if (self::$db === null) {
            throw new RuntimeException('ORM connection not set. Call Model::setConnection() in bootstrap.');
        }

        return self::$db->table(static::$table);
    }

    /**
     * @return list<static>
     */
    public static function all(): array
    {
        $rows = static::query()->orderBy(static::$primaryKey, 'DESC')->get();
        return array_map(static fn (array $row) => static::newFromRow($row), $rows);
    }

    public static function find(int $id): ?static
    {
        $row = static::query()->where(static::$primaryKey, $id)->first();

        return $row !== null ? static::newFromRow($row) : null;
    }

    public static function create(array $data): static
    {
        $model = new static();
        $model->fill($data);
        $model->save();

        return $model;
    }

    public static function count(): int
    {
        return count(static::query()->get());
    }

    protected static function newFromRow(array $row): static
    {
        $model = new static();
        $model->attributes = $row;
        $model->exists = true;

        return $model;
    }

    public function fill(array $data): self
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable, true) || array_key_exists($key, $this->attributes)) {
                $this->attributes[$key] = $value;
            }
        }

        return $this;
    }

    public function save(): bool
    {
        $data = $this->getSaveableData();

        if ($this->exists) {
            $id = $this->attributes[static::$primaryKey];
            $updated = static::query()
                ->where(static::$primaryKey, $id)
                ->update($data);

            return $updated > 0;
        }

        $id = static::query()->insert($data);
        $this->attributes[static::$primaryKey] = $id;
        $this->exists = true;

        return $id > 0;
    }

    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }

        $id = $this->attributes[static::$primaryKey];
        $deleted = static::query()->where(static::$primaryKey, $id)->delete();

        if ($deleted > 0) {
            $this->exists = false;
        }

        return $deleted > 0;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        if (in_array($name, $this->fillable, true) || $name === static::$primaryKey) {
            $this->attributes[$name] = $value;
        }
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getSaveableData(): array
    {
        $data = [];

        foreach ($this->fillable as $column) {
            if (array_key_exists($column, $this->attributes)) {
                $data[$column] = $this->attributes[$column];
            }
        }

        return $data;
    }
}
