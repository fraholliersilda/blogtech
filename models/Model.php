<?php
namespace Models;

use QueryBuilder\QueryBuilder;

class Model
{
    public $table;
    protected $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder();
    }

    // Find a single record by column and value
    public function findBy($column, $value)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['*'])
            ->where($column, '=', $value)
            ->limit(1)
            ->getOne();
    }

    // Insert a new record into the table
    public function create($data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->insert($data);
    }

    // Update an existing record by ID
    public function update($id, $data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->update($data)
            ->where('id', '=', $id)
            ->execute();
    }

    // Delete a record by ID
    public function delete($id)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('id', '=', $id)
            ->delete()
            ->execute();
    }

    // Start a select query for all columns
    public function select()
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['*']);
    }
}