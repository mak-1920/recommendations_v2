<?php

/** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace App\Entity\Review;

use App\Entity\Users\User;
use App\Repository\Review\RatingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'rating')]
#[ORM\Entity(repositoryClass: RatingRepository::class)]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $value;

    #[ORM\ManyToOne(targetEntity: Review::class, inversedBy: 'reviewRatings')]
    #[ORM\JoinColumn(nullable: false)]
    private Review $review;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $valuer;

    /**
     * @param Review $review
     * @param User $user
     * @param int $value
     */
    public function __construct(Review $review, User $user, int $value)
    {
        $this->review = $review;
        $this->valuer = $user;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Review
     */
    public function getReview(): Review
    {
        return $this->review;
    }

    /**
     * @param Review $review
     *
     * @return $this
     */
    public function setReview(Review $review): self
    {
        $this->review = $review;

        return $this;
    }

    /**
     * @return User
     */
    public function getValuer(): User
    {
        return $this->valuer;
    }

    /**
     * @param User $valuer
     *
     * @return $this
     */
    public function setValuer(User $valuer): self
    {
        $this->valuer = $valuer;

        return $this;
    }
}
