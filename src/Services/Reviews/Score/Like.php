<?php

declare(strict_types=1);

namespace App\Services\Reviews\Score;

use App\Entity\Review\Review;
use App\Entity\Users\User;
use App\Repository\Review\ReviewRepository;
use App\Services\Reviews\Exceptions\ReviewException;

class Like
{
    public function __construct(
        private ReviewHelper $reviewHelper,
        private ReviewRepository $repository,
    ) {
    }

    /**
     * @param Review $review
     * @param User|null $user
     *
     * @return bool
     */
    public function isLiked(Review $review, ?User $user): bool
    {
        if (null === $user) {
            return false;
        }

        return $review->getLikes()->contains($user);
    }

    /**
     * @param int $reviewId
     * @param User $user
     *
     * @return array{operation: string, review: Review}
     *
     * @throws ReviewException
     */
    public function set(int $reviewId, User $user): array
    {
        $review = $this->reviewHelper->get($reviewId);
        $result = ['review' => $review];

        if ($this->isLiked($review, $user)) {
            $this->removeLike($review, $user);
            $result['operation'] = 'remove';
        } else {
            $this->addLike($review, $user);
            $result['operation'] = 'add';
        }

        return $result;
    }

    private function removeLike(Review $review, User $user): void
    {
        $review->removeLike($user);
        $this->repository->update($review);
    }

    private function addLike(Review $review, User $user): void
    {
        $review->addLike($user);
        $this->repository->update($review);
    }
}
