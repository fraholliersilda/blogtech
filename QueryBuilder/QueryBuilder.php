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

    // Add reset method to clear state
    public function reset(): self
    {
        $this->table = '';
        $this->query = '';
        $this->bindings = [];
        $this->joinConditions = [];
        return $this;
    }

    public function table(string $table): self
    {
        // Reset state when starting a new query
        $this->reset();
        $this->table = $table;
        return $this;
    }

    public function select(array $columns = ['*']): self
    {
        $columnsList = implode(', ', $columns);
        $this->query = "SELECT $columnsList FROM {$this->table}";
        return $this;
    }

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

    public function update(array $data): self
    {
        $set = implode(', ', array_map(fn($col) => "$col = ?", array_keys($data)));
        $this->query = "UPDATE {$this->table} SET $set";

        // Reset bindings for update
        $this->bindings = array_values($data);

        return $this;
    }

    public function delete(): self
    {
        if (!str_contains($this->query, "WHERE")) {
            throw new PDOException("DELETE queries must include a WHERE clause to prevent accidental full-table deletions.");
        }
    
        error_log("Generated DELETE Query: " . $this->query);  
    
        $this->query = "DELETE FROM {$this->table} " . strstr($this->query, "WHERE");
    
        return $this;
    }

    // Fixed join method to handle both string conditions and closures
    public function join(string $table, $column1, string $operator = null, string $column2 = null, string $type = "INNER"): self
    {
        if (is_callable($column1)) {
            // Handle closure-based joins
            $joinBuilder = new JoinBuilder($table, $type);
            $column1($joinBuilder);
            $this->query .= $joinBuilder->buildJoin();
            $this->bindings = array_merge($this->bindings, $joinBuilder->getBindings());
        } else {
            // Handle simple string joins
            $this->query .= " $type JOIN $table ON $column1 $operator $column2";
        }
        return $this;
    }

    public function leftJoin(string $table, $column1, string $operator = null, string $column2 = null): self
    {
        if (is_callable($column1)) {
            // Handle closure-based joins
            $joinBuilder = new JoinBuilder($table, 'LEFT');
            $column1($joinBuilder);
            $this->query .= $joinBuilder->buildJoin();
            $this->bindings = array_merge($this->bindings, $joinBuilder->getBindings());
        } else {
            // Handle simple string joins
            $this->query .= " LEFT JOIN $table ON $column1 $operator $column2";
        }
        return $this;
    }

    // Added missing groupBy method
    public function groupBy(string $column): self
    {
        $this->query .= " GROUP BY $column";
        return $this;
    }

    public function limit(int $count): self
    {
        $this->query = preg_replace('/\s+LIMIT\s+\d+/i', '', $this->query);
        $this->query .= " LIMIT $count";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->query .= " ORDER BY $column $direction";
        return $this;
    }

    public function execute(): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare($this->query);

        return $stmt->execute($this->bindings);
    }

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

    public function getOne(): array
    {
        $results = $this->get();
        return $results ? $results[0] : [];
    }
}

// Helper class for handling complex join conditions
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

    public function on(string $column1, string $operator, string $column2): self
    {
        $this->conditions[] = "$column1 $operator $column2";
        return $this;
    }

    public function where(string $column, string $operator, mixed $value): self
    {
        $this->conditions[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function buildJoin(): string
    {
        $conditionsStr = implode(' AND ', $this->conditions);
        return " {$this->type} JOIN {$this->table} ON $conditionsStr";
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }
}