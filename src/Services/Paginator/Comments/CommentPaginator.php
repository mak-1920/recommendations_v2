<?php

declare(strict_types=1);

namespace App\Services\Paginator\Comments;

use App\Repository\Review\CommentRepository;
use App\Services\Paginator\PaginatorAdapter;
use App\Services\Paginator\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class CommentPaginator implements PaginatorInterface
{
    public function __construct(
        protected PaginatorAdapter $paginator,
        protected CommentRepository $repository,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @param array{reviewId: int} $data
     */
    public function paginate(int $page, int $lastId, array $data = [], int $rows = 10): SlidingPagination
    {
        $query = $this->repository->getQueryForPageWithComment($lastId, $data['reviewId']);

        return $this->paginator->paginate($page, $lastId, $query, $rows);
    }
}
