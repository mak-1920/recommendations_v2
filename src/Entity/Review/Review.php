<?php

/** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace App\Entity\Review;

use App\Entity\Users\User;
use App\Repository\Review\ReviewRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'review')]
#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'reviews', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $tags;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private Group $group;

    #[ORM\OneToMany(mappedBy: 'review', targetEntity: Illustration::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $illustrations;

    #[ORM\Column(type: 'datetimetz_immutable')]
    private DateTimeImmutable $dateOfPublication;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\Column(type: 'integer')]
    private int $authorRating;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'review', targetEntity: Rating::class, orphanRemoval: true)]
    private Collection $reviewRatings;

    #[ORM\OneToMany(mappedBy: 'review', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->illustrations = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->reviewRatings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeReview($this);
        }

        return $this;
    }

    /**
     * @param array $tags
     *
     * @return $this
     */
    public function setTags(array $tags): self
    {
        foreach ($this->tags as $tag) {
            $this->removeTag($tag);
        }

        foreach ($tags as $tag) {
            $this->addTag($tag);
        }

        return $this;
    }

    /**
     * @return Group
     */
    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * @param Group|null $group
     *
     * @return $this
     */
    public function setGroup(?Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return Collection<Illustration>
     */
    public function getIllustrations(): Collection
    {
        return $this->illustrations;
    }

    /**
     * @param Illustration $illustration
     *
     * @return $this
     */
    public function addIllustration(Illustration $illustration): self
    {
        if (!$this->illustrations->contains($illustration)) {
            $this->illustrations[] = $illustration;
            $illustration->setReview($this);
        }

        return $this;
    }

    /**
     * @param Illustration $illustration
     *
     * @return $this
     */
    public function removeIllustration(Illustration $illustration): self
    {
        if ($this->illustrations->removeElement($illustration)) {
            // set the owning side to null (unless already changed)
            if ($illustration->getReview() === $this) {
                $illustration->setReview(null);
            }
        }

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDateOfPublication(): DateTimeImmutable
    {
        return $this->dateOfPublication;
    }

    /**
     * @param DateTimeImmutable $dateOfPublication
     *
     * @return $this
     */
    public function setDateOfPublication(DateTimeImmutable $dateOfPublication): self
    {
        $this->dateOfPublication = $dateOfPublication;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     *
     * @return $this
     */
    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAuthorRating(): ?int
    {
        return $this->authorRating;
    }

    /**
     * @param int $authorRating
     *
     * @return $this
     */
    public function setAuthorRating(int $authorRating): self
    {
        $this->authorRating = $authorRating;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    /**
     * @param User $like
     *
     * @return $this
     */
    public function addLike(User $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
        }

        return $this;
    }

    /**
     * @param User $like
     *
     * @return $this
     */
    public function removeLike(User $like): self
    {
        $this->likes->removeElement($like);

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getReviewRatings(): Collection
    {
        return $this->reviewRatings;
    }

    /**
     * @param Rating $reviewRating
     *
     * @return $this
     */
    public function addReviewRating(Rating $reviewRating): self
    {
        if (!$this->reviewRatings->contains($reviewRating)) {
            $this->reviewRatings[] = $reviewRating;
            $reviewRating->setReview($this);
        }

        return $this;
    }

    /**
     * @param Rating $reviewRating
     *
     * @return $this
     */
    public function removeReviewRating(Rating $reviewRating): self
    {
        if ($this->reviewRatings->removeElement($reviewRating)) {
            if ($reviewRating->getReview() === $this) {
                $reviewRating->setReview(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }
}
