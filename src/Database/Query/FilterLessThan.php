<?php

namespace App\Database\Query;

class FilterLessThan extends Filter
{
    // col = '<100'

    protected $pattern = '<([0-9]*)';

    function isValidFilter(): bool
    {
        return count($this->matches) === 2;
    }

    function getCondition(string $column): string
    {
        $val = $this->matches[1];

        return " round({$column},  5) < {$val} ";
    }
}