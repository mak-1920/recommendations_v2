<?php

declare(strict_types=1);

namespace App\Services\Reviews\Score;

use App\Entity\Review\Review;
use App\Repository\Review\ReviewRepository;
use App\Services\Reviews\Exceptions\InvalidReviewIDException;
use App\Services\Reviews\Exceptions\ReviewException;
use Exception;

class ReviewHelper
{
    public function __construct(
        private ReviewRepository $repository,
    ) {
    }

    /**
     * @param int $reviewId
     *
     * @return Review
     *
     * @throws ReviewException
     */
    public function get(int $reviewId): Review
    {
        try {
            $review = $this->repository->getReviewWithScore($reviewId);

            if (is_null($review)) {
                throw new InvalidReviewIDException('Review with id'.$reviewId.' not found!');
            }
        } catch (Exception $e) {
            throw new ReviewException($e->getMessage(), previous: $e);
        }

        return $review;
    }
}
