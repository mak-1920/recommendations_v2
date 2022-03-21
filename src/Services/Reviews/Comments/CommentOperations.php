<?php

declare(strict_types=1);

namespace App\Services\Reviews\Comments;

use App\Entity\Review\Comment;
use App\Entity\Review\Review;
use App\Entity\Users\User;
use App\Repository\Review\CommentRepository;
use App\Repository\Review\ReviewRepository;
use App\Services\Indexer\IndexerInterface;
use App\Services\Reviews\Comments\Exceptions\CommentsException;
use App\Services\Reviews\Comments\Exceptions\EmptyMessageException;
use App\Services\Reviews\Exceptions\InvalidReviewIDException;
use App\Services\Reviews\Exceptions\InvalidUserException;
use App\Services\Reviews\Exceptions\ReviewException;
use App\Services\Senders\SenderInterface;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;

class CommentOperations implements OperationsInterface
{
    public function __construct(
        private IndexerInterface $indexer,
        private SenderInterface $sender,
        private CommentRepository $commentRepository,
        private ReviewRepository $reviewRepository,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidReviewIDException
     * @throws EmptyMessageException
     */
    public function create(int $reviewId, User $user, string $text): Comment
    {
        if (0 === mb_strlen(trim($text))) {
            throw new EmptyMessageException();
        }

        $review = $this->getReview($reviewId);
        $comment = $this->initComment($review, $user, $text);

        $this->commentRepository->addComment($comment);
        $this->indexer->edit($comment->getReview());
        $this->sender->publish($this->getSenderUrl($comment, 'create'), $comment);

        return $comment;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ReviewException
     */
    public function remove(int $commentId, User $user): bool
    {
        ['author_id' => $authorId, 'comment' => $comment] = $this->getCommentInfo($commentId);
        if (!$this->checkAuthor($authorId, $user)) {
            throw new InvalidUserException('This user is not author of comment!');
        }

        $this->indexer->remove($comment->getReview());
        $this->sender->publish($this->getSenderUrl($comment, 'remove'), $comment);
        $this->commentRepository->remove($comment);

        return true;
    }

    /**
     * @param Review $review
     * @param User $user
     * @param string $text
     *
     * @return Comment
     */
    private function initComment(Review $review, User $user, string $text): Comment
    {
        $comment = new Comment();

        $comment->setAuthor($user);
        $comment->setReview($review);
        $comment->setText($text);
        $comment->setTime(new DateTimeImmutable());

        return $comment;
    }

    /**
     * @param int $reviewId
     *
     * @return Review
     *
     * @throws InvalidReviewIDException
     */
    private function getReview(int $reviewId): Review
    {
        $review = $this->reviewRepository->find($reviewId);

        if (is_null($review)) {
            throw new InvalidReviewIDException('Review with id'.$reviewId.' not found!');
        }

        return $review;
    }

    /**
     * @param Comment $comment
     * @param string $operation
     *
     * @return string
     */
    private function getSenderUrl(Comment $comment, string $operation): string
    {
        return sprintf(
            '/review/%d/comment/%d/%s',
            $comment->getReview()->getId(),
            $comment->getId(),
            $operation,
        );
    }

    /**
     * @param int $commentId
     *
     * @return array{comment: Comment, author_id: int}
     *
     * @throws CommentsException
     */
    private function getCommentInfo(int $commentId): array
    {
        try {
            $comment = $this->commentRepository->findByIdWithAuthorId($commentId);
            if (is_null($comment)) {
                throw new CommentsException('Invalid id of comment: '.$commentId);
            }
        } catch (NonUniqueResultException $e) {
            throw new CommentsException($e->getMessage(), previous: $e);
        }

        return $comment;
    }

    /**
     * @param int $authorId
     * @param User $user
     *
     * @return bool
     */
    private function checkAuthor(int $authorId, User $user): bool
    {
        return $authorId === $user->getId() || in_array(User::ROLE_ADMIN, $user->getRoles());
    }
}
