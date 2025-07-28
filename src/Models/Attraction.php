<?php

namespace App\Models;

class Attraction
{
    /**
     * уникальный идентификатор
     * @var
     */
    public $id;
    /**
     * название, строка
     * @var
     */
    public $name;
    /**
     * удалённость от центра города, число
     * @var
     */
    public $distance_from_center;
    /**
     * связь с городом
     * @var
     */
    public $city_id;

    public function __construct($id, $name, $distance_from_center, $city_id)
    {
        $this->id = $id;
        $this->name = $name;
        $this->distance_from_center = $distance_from_center;
        $this->city_id = $city_id;
    }
}