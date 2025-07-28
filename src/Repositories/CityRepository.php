<?php

namespace App\Repositories;

use App\Models\City;

class CityRepository extends BaseRepository
{
    public function __construct(
        string $table = 'cities',
        array $columns = ['id', 'name']
    )
    {
        parent::__construct($table, $columns);
    }

    public function findById($id): ?City
    {
        $row = $this->rowById($id);

        if (!$row) {
            return null;
        }

        return new City($row['id'], $row['name']);
    }

    public function save(City $city): City
    {
        if ($city->id) {
            $this->raw("UPDATE {$this->table} SET name = :name")
                ->addParams(['name' => $city->name])
                ->filter('id', $city->id)
                ->queryExec($this->pdo);
        } else {
            $this->raw("INSERT INTO {$this->table} (name) VALUES (:name)")
                ->addParams(['name' => $city->name])
                ->queryExec($this->pdo);

            $city->id = $this->pdo->lastInsertId();
        }

        return $city;
    }
}