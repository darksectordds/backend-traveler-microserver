<?php

namespace App\Database;

use App\Database\Query\Filter;

class QueryFilterManager
{
    private $filters = [];

    public function __construct(...$filters)
    {
        $this->filters = $filters;
    }

    public function addFilter(Filter $filter): void
    {
        $this->filters[] = $filter;
    }

    public function getMatchCondition(string $param, string $column): ?string
    {
        /** @var Filter $filter */
        foreach ($this->filters as $filter) {
            $filter->verify($param);

            if ($filter->isValidFilter()) {
                return $filter->getCondition($column);
            }
        }

        return null;
    }
}