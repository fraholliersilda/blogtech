<?php
namespace QueryBuilder;

use Database;
use PDO;
use PDOException;

class QueryBuilder
{
    private string $table;
    private string $query = '';
    private array $bindings = [];
    private array $joinConditions = [];

    // Reset query builder state for reuse
    public function reset(): self
    {
        $this->table = '';
        $this->query = '';
        $this->bindings = [];
        $this->joinConditions = [];
        return $this;
    }

    // Set the table for the query
    public function table(string $table): self
    {
        $this->reset();
        $this->table = $table;
        return $this;
    }

    // Build SELECT query with specified columns
    public function select(array $columns = ['*']): self
    {
        $columnsList = implode(', ', $columns);
        $this->query = "SELECT $columnsList FROM {$this->table}";
        return $this;
    }

    // Add WHERE condition to query with parameter binding
    public function where(string $column, string $operator, mixed $value): self
    {
        $placeholder = "?";
        $this->query .= (str_contains($this->query, 'WHERE') ? " AND" : " WHERE") . " $column $operator $placeholder";
        $this->bindings[] = $value;

        error_log("WHERE Condition: $column $operator $value");
        error_log("Current Query: " . $this->query);
        error_log("Bindings: " . print_r($this->bindings, true));

        return $this;
    }

    // Insert data into table and return last insert ID
    public function insert(array $data): mixed
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($col) => ":$col", array_keys($data)));
        $this->query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $this->bindings = array_combine(array_map(fn($col) => ":$col", array_keys($data)), array_values($data));

        if ($this->execute()) {
            $pdo = Database::getConnection();
            return $pdo->lastInsertId();
        }

        return false;
    }

    // Build UPDATE query with data
    public function update(array $data): self
    {
        $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $this->query = "UPDATE {$this->table} SET $set";
        $this->bindings = array_values($data);
        return $this;
    }

    // Build DELETE query with required WHERE clause for safety
    public function delete(): self
    {
        if (!str_contains($this->query, "WHERE")) {
            throw new PDOException("DELETE queries must include a WHERE clause to prevent accidental full-table deletions.");
        }

        error_log("Generated DELETE Query: " . $this->query);

        $this->query = "DELETE FROM {$this->table} " . strstr($this->query, "WHERE");

        return $this;
    }

    // Add INNER JOIN to query with support for callable join conditions
    public function join(string $table, $column1, ?string $operator = null, ?string $column2 = null, string $type = "INNER"): self
    {
        if (is_callable($column1)) {
            $joinBuilder = new JoinBuilder($table, $type);
            $column1($joinBuilder);
            $this->query .= $joinBuilder->buildJoin();
            $this->bindings = array_merge($this->bindings, $joinBuilder->getBindings());
        } else {
            $this->query .= " $type JOIN $table ON $column1 $operator $column2";
        }
        return $this;
    }

    // Add LEFT JOIN to query with support for callable join conditions
    public function leftJoin(string $table, $column1, ?string $operator = null, ?string $column2 = null): self
    {
        if (is_callable($column1)) {
            $joinBuilder = new JoinBuilder($table, 'LEFT');
            $column1($joinBuilder);
            $this->query .= $joinBuilder->buildJoin();
            $this->bindings = array_merge($this->bindings, $joinBuilder->getBindings());
        } else {
            $this->query .= " LEFT JOIN $table ON $column1 $operator $column2";
        }
        return $this;
    }

    // Add GROUP BY clause to query
    public function groupBy(string $column): self
    {
        $this->query .= " GROUP BY $column";
        return $this;
    }

    // Add LIMIT clause to query, removing any existing limit
    public function limit(int $count): self
    {
        $this->query = preg_replace('/\s+LIMIT\s+\d+/i', '', $this->query);
        $this->query .= " LIMIT $count";
        return $this;
    }

    // Add ORDER BY clause to query
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->query .= " ORDER BY $column $direction";
        return $this;
    }

    // Execute query without returning results
    public function execute(): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare($this->query);
        return $stmt->execute($this->bindings);
    }

    // Execute query and return all results
    public function get(): array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare($this->query);
            error_log("Executing Query: " . $this->query);
            error_log("Executing Bindings: " . print_r($this->bindings, true));

            $stmt->execute($this->bindings);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $results ?: [];
        } catch (PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return [];
        }
    }

    // Execute query and return first result
    public function getOne(): array
    {
        $results = $this->get();
        return $results ? $results[0] : [];
    }
}

// Helper class for building complex JOIN conditions
class JoinBuilder
{
    private string $table;
    private string $type;
    private array $conditions = [];
    private array $bindings = [];

    public function __construct(string $table, string $type)
    {
        $this->table = $table;
        $this->type = $type;
    }

    // Add ON condition to join (column to column comparison)
    public function on(string $column1, string $operator, string $column2): self
    {
        $this->conditions[] = "$column1 $operator $column2";
        return $this;
    }

    // Add WHERE condition to join (column to value comparison)
    public function where(string $column, string $operator, mixed $value): self
    {
        $this->conditions[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    // Build the complete JOIN clause string
    public function buildJoin(): string
    {
        $conditionsStr = implode(' AND ', $this->conditions);
        return " {$this->type} JOIN {$this->table} ON $conditionsStr";
    }

    // Return bindings for prepared statement
    public function getBindings(): array
    {
        return $this->bindings;
    }
}