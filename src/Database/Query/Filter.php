<?php

namespace App\Database\Query;

abstract class Filter
{
    protected $pattern;

    protected $matches;

    public function verify(string $str): void
    {
        preg_match("/{$this->pattern}/", $str, $this->matches);
    }

    abstract function isValidFilter(): bool;

    abstract function getCondition(string $column): string;
}