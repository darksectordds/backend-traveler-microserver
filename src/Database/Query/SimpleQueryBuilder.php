<?php

namespace App\Database\Query;

trait SimpleQueryBuilder
{
    protected $query;
    protected $queryPaginate;

    protected $params = [];

    protected $conditions = [];

    protected function addParams(array $params): self
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    protected function addConditions(array $conditions): self
    {
        $this->conditions = array_merge($this->conditions, $conditions);

        return $this;
    }

    protected function raw(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    protected function select(string $select): self
    {
        $this->query .= "SELECT $select";

        return $this;
    }

    protected function delete(): self
    {
        $this->query .= "DELETE";

        return $this;
    }

    protected function from(string $table): self
    {
        $this->query .= " FROM $table";

        return $this;
    }

    protected function filter(string $column, string $value): self
    {
        $this->conditions[] = "$column = :$column";
        $this->params[$column] = $value;

        return $this;
    }

    protected function withCallback(callable $callback): self
    {
        $callback($this);

        return $this;
    }

    protected function orderBy(string $column, string $direction = 'ASC'): self
    {
        $validColumns = $this->getColumns();

        if (in_array($column, $validColumns)) {
            $this->query .= " ORDER BY {$column} {$direction}";
        }

        return $this;
    }

    protected function paginate(int $page = 1, int $perPage = 10): self
    {
        $offset = ($page - 1) * $perPage;

        $this->queryPaginate = " LIMIT :limit OFFSET :offset";
        $this->params["limit"] = $perPage;
        $this->params["offset"] = $offset;

        return $this;
    }

    public function queryExec(\PDO $pdo): \PDOStatement
    {
        if (!empty($this->conditions)) {
            $this->query .= " WHERE " . implode(" AND ", $this->conditions);
        }
        if (!empty($this->queryPaginate)) {
            $this->query .= $this->queryPaginate;
        }

        $query = $this->query;

//        echo $query.PHP_EOL;
//        echo var_dump($this->params);

        $this->query = '';
        $this->queryPaginate = '';

        $stmt = $pdo->prepare($query);
        $stmt->execute($this->params);

        $this->conditions = [];
        $this->params = [];

        return $stmt;
    }
}