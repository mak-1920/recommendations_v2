<?php

declare(strict_types=1);

namespace App\Services\Paginator\Reviews;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class AllReviewsPaginator extends ReviewsPaginator
{
    /**
     * {@inheritdoc}
     *
     * @param array{orderBy: string} $data
     */
    public function paginate(int $page, int $lastId, array $data = [], int $rows = 10): SlidingPagination
    {
        $query = $this->repository->getLastReviews($lastId, $data['orderBy']);

        return $this->paginator->paginate($page, $lastId, $query, $rows);
    }
}
