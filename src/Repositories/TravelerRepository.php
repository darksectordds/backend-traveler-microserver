<?php

namespace App\Repositories;

use App\Models\Traveler;

class TravelerRepository extends BaseRepository
{
    public function __construct(
        string $table = 'travelers',
        array $columns = ['id', 'name']
    )
    {
        parent::__construct($table, $columns);
    }

    public function findById($id): ?Traveler
    {
        $row = $this->rowById($id);

        if (!$row) {
            return null;
        }

        return new Traveler($row['id'], $row['name']);
    }

    public function save(Traveler $traveler): Traveler
    {
        if ($traveler->id) {
            $this->raw("UPDATE {$this->table} SET name = :name")
                ->addParams(['name' => $traveler->name])
                ->filter('id', $traveler->id)
                ->queryExec($this->pdo);
        } else {
            $this->raw("INSERT INTO {$this->table} (name) VALUES (:name)")
                ->addParams(['name' => $traveler->name])
                ->queryExec($this->pdo);

            $traveler->id = $this->pdo->lastInsertId();
        }

        return $traveler;
    }
}