<?php

declare(strict_types=1);

namespace App\Services\Senders\Mercure\Comments;

use App\Entity\Review\Comment;
use App\Entity\Users\User;
use App\Services\Senders\Mercure\MercureSender;
use DateTimeInterface;
use Symfony\Component\Mercure\Update;

/**
 * @extends MercureSender<Comment>
 */
class CommentsEditSender extends MercureSender
{
    /**
     * {@inheritDoc}
     */
    public function publish(string $path, mixed $obj): void
    {
        $update = new Update(
            $path,
            $this->getMessage($obj),
        );
        $this->hub->publish($update);
    }

    /**
     * @param Comment $obj
     */
    protected function getMessage(mixed $obj): string
    {
        $author = $obj->getAuthor();

        $data = [
            'comment' => [
                'id' => $obj->getId(),
                'text' => $obj->getText(),
                'time' => $obj->getTime()->format(DateTimeInterface::ISO8601),
            ],
            'author_name' => $author->getNickname(),
            'author_id' => $author->getId(),
            'author_likes' => $this->getAuthorLikesCount($author),
        ];

        return json_encode($data);
    }

    private function getAuthorLikesCount(User $user): int
    {
        $sum = 0;

        foreach ($user->getReviews() as $review) {
            $sum += $review->getLikes()->count();
        }

        return $sum;
    }
}
