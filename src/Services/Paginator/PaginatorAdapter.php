<?php

declare(strict_types=1);

namespace App\Services\Paginator;

use Doctrine\ORM\Query;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class PaginatorAdapter implements PaginatorAdapterInterface
{
    public function __construct(
        private \Knp\Component\Pager\PaginatorInterface $paginator,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(int $page, int $lastId, Query $query, int $rows = 10): SlidingPagination
    {
        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate(
            $query,
            $page,
            $rows,
            [
                'wrap-queries' => true,
            ]
        );
        $pagination->setParam('last', $lastId);

        return $pagination;
    }
}
