<?php

declare(strict_types=1);

namespace App\Services\Paginator\Reviews;

use App\Repository\Review\ReviewRepository;
use App\Services\Paginator\PaginatorAdapter;
use App\Services\Paginator\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

abstract class ReviewsPaginator implements PaginatorInterface
{
    public const REVIEW_SORT_TYPES = ['relevance', 'rating'];

    public const REVIEWS_ON_PAGE = 10;

    public function __construct(
        protected PaginatorAdapter $paginator,
        protected ReviewRepository $repository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    abstract public function paginate(int $page, int $lastId, array $data = [], int $rows = 10): SlidingPagination;
}
