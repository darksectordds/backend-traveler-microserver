<?php

namespace App\Repositories;

use App\Models\Rating;

class RatingRepository extends BaseRepository
{
    public function __construct(
        string $table = 'ratings',
        array $columns = ['id', 'traveler_id', 'attraction_id', 'score']
    )
    {
        parent::__construct($table, $columns);
    }

    public function findById($id): ?Rating
    {
        $row = $this->rowById($id);

        if (!$row) {
            return null;
        }

        return new Rating($row['id'], $row['traveler_id'], $row['attraction_id'], $row['score']);
    }

    public function save(Rating $rating): Rating
    {
        if ($rating->id) {
            $this->raw("UPDATE {$this->table} SET traveler_id = :traveler_id, attraction_id = :attraction_id, score = :score")
                ->addParams([
                    'traveler_id' => $rating->traveler_id,
                    'attraction_id' => $rating->attraction_id,
                    'score' => $rating->score,
                ])
                ->filter('id', $rating->id)
                ->queryExec($this->pdo);
        } else {
            $this->raw("INSERT INTO {$this->table} (traveler_id,attraction_id,score) VALUES (:traveler_id,:attraction_id,:score)")
                ->addParams([
                    'traveler_id' => $rating->traveler_id,
                    'attraction_id' => $rating->attraction_id,
                    'score' => $rating->score,
                ])
                ->queryExec($this->pdo);

            $rating->id = $this->pdo->lastInsertId();
        }

        return $rating;
    }
}