<?php

/** @noinspection ALL */

declare(strict_types=1);

namespace App\Entity\Review;

use App\Repository\Review\IllustrationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'illustration')]
#[ORM\Entity(repositoryClass: IllustrationRepository::class)]
class Illustration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $img;

    #[ORM\ManyToOne(targetEntity: Review::class, inversedBy: 'illustrations')]
    private $review;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getImg(): string
    {
        return $this->img;
    }

    /**
     * @param string $img
     *
     * @return $this
     */
    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    /**
     * @return Review|null
     */
    public function getReview(): ?Review
    {
        return $this->review;
    }

    /**
     * @param Review|null $review
     *
     * @return $this
     */
    public function setReview(?Review $review): self
    {
        $this->review = $review;

        return $this;
    }
}
