<?php

namespace App\Repositories;

use App\Database\Query\FilterBetween;
use App\Database\Query\FilterLessThan;
use App\Database\Query\FilterMoreThan;
use App\Database\QueryFilterManager as DBQueryFilterManager;
use App\Models\Attraction;

class AttractionRepository extends BaseRepository
{
    public function __construct(
        string $table = 'attractions',
        array $columns = [
            'id', 'name', 'distance_from_center', 'city_id',
        ]
    )
    {
        parent::__construct($table, $columns);
    }

    public function findAllWithFilters(array $params = [], int $page = 1, int $perPage = 10): array
    {
        $funcQueryFilterCallback = function (BaseRepository &$repository) use ($params) {
            // фильтр по городу
            if (isset($params['city_id'])) {
                $repository->filter('city_id', $params['city_id']);
            }
            // фильтр по | > | < | between
            if (isset($params['distance_from_center'])) {
                /**
                 * SimpleQueryBuilder был написан намного позже.
                 * Хорошим тоном было бы переписать эту часть и положить туда.
                 * Но... Я не буду. Ради какой-то жалкой "возможности" с кем-то
                 * поработать.
                 *
                 * И так убил кучу времени в пустую на какую-то ерунду.
                 * Не обесуйте - время деньги.
                 */
                $DBQueryFilterManager = new DBQueryFilterManager(
                    new FilterLessThan(), new FilterMoreThan(), new FilterBetween()
                );

                $condition = $DBQueryFilterManager->getMatchCondition($params['distance_from_center'], 'distance_from_center');

                if (isset($condition)) {
                    $repository->addConditions([$condition]);
                }
            }
            if (isset($params['sort_by'])) {
                $repository->orderBy($params['sort_by']);
            }
        };

        $stmt = $this->select('*')
            ->from($this->table)
            ->withCallback($funcQueryFilterCallback)
            ->paginate($page, $perPage)
            ->queryExec($this->pdo);

        $items = $stmt->fetchAll();

        $stmt = $this->select('COUNT(*)')
            ->from($this->table)
            ->withCallback($funcQueryFilterCallback)
            ->queryExec($this->pdo);
        $total = (int)$stmt->fetchColumn();

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    public function findById($id): ?Attraction
    {
        $row = $this->rowById($id);

        if (!$row) {
            return null;
        }

        return new Attraction($row['id'], $row['name'], $row['distance_from_center'], $row['city_id']);
    }

    public function save(Attraction $attraction): Attraction
    {
        if ($attraction->id) {
            $this->raw("UPDATE {$this->table} SET name = :name, distance_from_center = :distance_from_center, city_id = :city_id")
                ->addParams([
                    'name' => $attraction->name,
                    'distance_from_center' => $attraction->distance_from_center,
                    'city_id' => $attraction->city_id
                ])
                ->filter('id', $attraction->id)
                ->queryExec($this->pdo);
        } else {
            $this->raw("INSERT INTO {$this->table} (name,distance_from_center,city_id) VALUES (:name,:distance_from_center,:city_id)")
                ->addParams([
                    'name' => $attraction->name,
                    'distance_from_center' => $attraction->distance_from_center,
                    'city_id' => $attraction->city_id
                ])
                ->queryExec($this->pdo);

            $attraction->id = $this->pdo->lastInsertId();
        }

        return $attraction;
    }
}