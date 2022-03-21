<?php

declare(strict_types=1);

namespace App\Services\Paginator;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

interface PaginatorInterface
{
    /**
     * @param int $page
     * @param int $lastId
     * @param array $data
     * @param int $rows
     *
     * @return SlidingPagination
     */
    public function paginate(int $page, int $lastId, array $data = [], int $rows = 10): SlidingPagination;
}
