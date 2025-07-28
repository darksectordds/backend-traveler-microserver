<?php

namespace App\Repositories;

use App\Database\Database;
use App\Database\Query\SimpleQueryBuilder;
use PDO;

class BaseRepository
{
    use SimpleQueryBuilder;

    protected string $table;

    protected array $columns;

    protected PDO $pdo;

    public function __construct(string $table, array $columns)
    {
        $this->table = $table;
        $this->columns = $columns;

        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function findAll(): array|false
    {
        $stmt = $this->select('*')
            ->from($this->table)
            ->queryExec($this->pdo);

        return $stmt->fetchAll();
    }

    public function getPaginate(int $page = 1, int $perPage = 10): array
    {
        $stmt = $this->select('*')
            ->from($this->table)
            ->paginate($page, $perPage)
            ->queryExec($this->pdo);
        $items = $stmt->fetchAll();

        $stmt = $this->select('COUNT(*)')
            ->from($this->table)
            ->queryExec($this->pdo);
        $total = (int)$stmt->fetchColumn();

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    public function rowById($id): mixed
    {
        $stmt = $this->select('*')
            ->from($this->table)
            ->filter('id', $id)
            ->queryExec($this->pdo);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}