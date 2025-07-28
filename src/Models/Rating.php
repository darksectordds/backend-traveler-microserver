<?php

namespace App\Models;

class Rating
{
    /**
     * Уникальный идентификатор
     *
     * @var
     */
    public $id;
    /**
     * Связь с путешественником
     *
     * @var
     */
    public $traveler_id;
    /**
     * Связь с достопримечательностью
     *
     * @var
     */
    public $attraction_id;
    /**
     * Оценка от [1..5]
     *
     * @var
     */
    public $score;

    public function __construct($id, $traveler_id, $attraction_id, $score)
    {
        $this->id = $id;
        $this->traveler_id = $traveler_id;
        $this->attraction_id = $attraction_id;
        $this->score = $score;
    }
}