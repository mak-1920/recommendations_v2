<?php

declare(strict_types=1);

namespace App\Services\Paginator;

use Doctrine\ORM\Query;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

interface PaginatorAdapterInterface
{
    /**
     * @param int $page
     * @param int $lastId
     * @param Query $query
     * @param int $rows
     *
     * @return SlidingPagination
     */
    public function paginate(int $page, int $lastId, Query $query, int $rows = 10): SlidingPagination;
}
