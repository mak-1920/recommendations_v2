<?php

declare(strict_types=1);

namespace App\Services\Reviews\Score;

use App\Entity\Review\Rating as EntRating;
use App\Entity\Review\Review;
use App\Entity\Users\User;
use App\Repository\Review\RatingRepository;
use App\Services\Reviews\Exceptions\ReviewException;

class Rating
{
    public function __construct(
        private RatingRepository $repository,
        private ReviewHelper $reviewHelper,
    ) {
    }

    /**
     * @param Review $review
     * @param User|null $user
     *
     * @return int
     */
    public function getRatingByUser(Review $review, ?User $user): int
    {
        if (null === $user) {
            return 0;
        }

        $rating = $review->getReviewRatings()
            ->filter(fn (EntRating $r) => $r->getValuer() === $user)
            ->first();

        return $rating ? $rating->getValue() : 0;
    }

    /**
     * @param int $reviewId
     * @param User $user
     * @param int $value
     *
     * @return bool true if add rating, false if remove rating
     *
     * @throws ReviewException
     */
    public function set(int $reviewId, User $user, int $value): bool
    {
        $review = $this->reviewHelper->get($reviewId);
        $rating = $this->getRating($review, $user);

        if (is_null($rating)) {
            $this->add($user, $review, $value);

            return true;
        } else {
            $this->remove($rating);

            return false;
        }
    }

    /**
     * @param Review $review
     * @param User $user
     *
     * @return EntRating|null
     */
    private function getRating(Review $review, User $user): ?EntRating
    {
        $rating = $review->getReviewRatings()
            ->filter(fn (EntRating $r) => $r->getValuer() === $user)
            ->first();

        return $rating ?: null;
    }

    /**
     * @param EntRating $rating
     *
     * @return void
     */
    private function remove(EntRating $rating): void
    {
        $this->repository->remove($rating);
    }

    /**
     * @param User $user
     * @param Review $review
     * @param int $value
     *
     * @return void
     */
    private function add(User $user, Review $review, int $value): void
    {
        $rating = $this->createRating($user, $review, $value);

        $this->repository->add($rating);
    }

    /**
     * @param User $user
     * @param Review $review
     * @param int $value
     *
     * @return EntRating
     */
    private function createRating(User $user, Review $review, int $value): EntRating
    {
        return new EntRating($review, $user, $value);
    }
}
