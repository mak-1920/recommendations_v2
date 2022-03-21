<?php

declare(strict_types=1);

namespace App\Services\Paginator\Reviews;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class ReviewsByTagPaginator extends ReviewsPaginator
{
    /**
     * {@inheritDoc}
     *
     * @param array{tag: string} $data
     */
    public function paginate(int $page, int $lastId, array $data = [], int $rows = 10): SlidingPagination
    {
        $query = $this->repository->getReviewsByTag($lastId, $data['tag']);

        return $this->paginator->paginate($page, $lastId, $query, $rows);
    }
}
