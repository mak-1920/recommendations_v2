<?php

declare(strict_types=1);

namespace App\Services\Searcher;

interface SearcherInterface
{
    /**
     * @param string $query
     * @return int
     */
    public function getResultsCount(string $query): int;

    /**
     * @param string $query
     * @param int $page
     * @param int $rows
     * @return array
     */
    public function getResultsByPage(string $query, int $page, int $rows = 10): array;
}