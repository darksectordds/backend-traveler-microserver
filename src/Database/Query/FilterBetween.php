<?php

namespace App\Database\Query;

class FilterBetween extends Filter
{
    // col = '0-100'

    protected $pattern = '([0-9]*)-([0-9]*)';

    function isValidFilter(): bool
    {
        return count($this->matches) === 3;
    }

    function getCondition(string $column): string
    {
        $less = $this->matches[1];
        $greater = $this->matches[2];

        return " round({$column},  5) BETWEEN {$less} AND {$greater} ";
    }
}