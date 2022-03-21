<?php

declare(strict_types=1);

namespace App\Services\Reviews\Comments;

use App\Entity\Review\Comment;
use App\Entity\Users\User;

interface OperationsInterface
{
    /**
     * @param int $reviewId
     * @param User $user
     * @param string $text
     *
     * @return Comment
     */
    public function create(int $reviewId, User $user, string $text): Comment;

    /**
     * @param int $commentId
     * @param User $user
     *
     * @return bool
     */
    public function remove(int $commentId, User $user): bool;
}
