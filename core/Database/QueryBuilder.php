<?php

declare(strict_types=1);

namespace Core\Database;

use PDO;

/**
 * Small query builder - not fancy but works for our CRUD app.
 */
final class QueryBuilder
{
    private string $table;
    private array $wheres = [];
    private array $bindings = [];
    private ?string $orderColumn = null;
    private string $orderDirection = 'ASC';

    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function table(string $table): self
    {
        $clone = clone $this;
        $clone->table = $table;
        $clone->wheres = [];
        $clone->bindings = [];
        $clone->orderColumn = null;
        $clone->orderDirection = 'ASC';

        return $clone;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderColumn = $column;
        $this->orderDirection = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        return $this;
    }

    public function where(string $column, mixed $value): self
    {
        $this->wheres[] = "{$column} = ?";
        $this->bindings[] = $value;

        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($this->wheres !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
        if ($this->orderColumn !== null) {
            $sql .= " ORDER BY {$this->orderColumn} {$this->orderDirection}";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->fetchAll();
    }

    public function first(): ?array
    {
        $rows = $this->get();
        return $rows[0] ?? null;
    }

    public function insert(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));

        return (int) $this->pdo->lastInsertId();
    }

    public function update(array $data): int
    {
        $sets = [];
        $values = [];

        foreach ($data as $col => $val) {
            $sets[] = "{$col} = ?";
            $values[] = $val;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets);
        if ($this->wheres !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([...$values, ...$this->bindings]);

        return $stmt->rowCount();
    }

    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";
        if ($this->wheres !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->rowCount();
    }
}

