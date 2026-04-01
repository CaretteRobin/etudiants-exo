<?php

namespace App;

class MySQLQueryBuilder implements QueryBuilderInterface
{
    private array $fields = [];
    private string $table = '';
    private array $conditions = [];

    public function select(array $fields): QueryBuilderInterface
    {
        $this->fields = $fields;

        return $this;
    }

    public function from(string $table): QueryBuilderInterface
    {
        $this->table = $table;

        return $this;
    }

    public function where(string $condition): QueryBuilderInterface
    {
        $this->conditions[] = $condition;

        return $this;
    }

    public function build(): string
    {
        $query = 'SELECT ';
        $query .= empty($this->fields) ? '*' : implode(', ', $this->fields);
        $query .= ' FROM ' . $this->table;

        if (!empty($this->conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $this->conditions);
        }

        return $query . ';';
    }
}
