<?php

declare(strict_types=1);

namespace App\Repository\Review;

use App\Entity\Review\Review;
use App\Entity\Users\User;
use App\Services\Paginator\Reviews\ReviewsPaginator;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[] findAll()
 * @method Review[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @return int
     */
    public function getLastReviewID(): int
    {
        try {
            return $this->createQueryBuilder('r')
                ->select('r.id')
                ->orderBy('r.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (Exception $e) {
            return -1;
        }
    }

    /**
     * @param int $id
     *
     * @return array{author_id: int, author_likes: int, author_name: string, avgRating: float, group: string, review: Review}|null
     *
     * @throws NonUniqueResultException
     */
    public function findByID(int $id): ?array
    {
        return $this->getMainQuery()
            ->addSelect('i')
            ->leftJoin('r.illustrations', 'i')
            ->addGroupBy('i')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $lastId
     * @param string $sortType
     * @param int $userId
     *
     * @return Query
     */
    public function getLastReviews(int $lastId, string $sortType, int $userId = -1): Query
    {
        $qb = $this->getMainQuery();

        if (-1 !== $lastId) {
            $qb->andWhere('r.id <= :lastId')
                ->setParameter('lastId', $lastId);
        }

        if (-1 !== $userId) {
            $qb->andWhere('a.id = :author')
                ->setParameter('author', $userId);
        }

        if ($sortType == ReviewsPaginator::REVIEW_SORT_TYPES[1]) {
            $qb->orderBy('avgRating', 'DESC');
        } else {
            $qb->orderBy('r.id', 'DESC');
        }

        return $qb->getQuery();
    }

    /**
     * @param int $lastId
     * @param string $tagName
     *
     * @return Query
     */
    public function getReviewsByTag(int $lastId, string $tagName): Query
    {
        $expr = $this->_em->getExpressionBuilder();
        $qb = $this->getMainQuery()
            ->where(
                $expr->in(
                    'r.id',
                    $this->createQueryBuilder('sr')
                        ->select('sr.id')
                        ->leftjoin('sr.tags', 'st')
                        ->where('st.name = :tagName')
                        ->getDQL()
                )
            )
            ->setParameter('tagName', $tagName)
            ;

        if (-1 !== $lastId) {
            $qb->andWhere('r.id <= :lastId')
                ->setParameter('lastId', $lastId);
        }

        return $qb->getQuery();
    }

    /**
     * @param int $reviewId
     *
     * @return Review|null
     *
     * @throws NonUniqueResultException
     */
    public function getReviewWithScore(int $reviewId): ?Review
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.reviewRatings', 'rr')
            ->leftJoin('r.likes', 'rl')
            ->where('r.id = :id')
            ->setParameter('id', $reviewId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Review $review
     * @param User $user
     * @param array $tags
     *
     * @return Review
     * @TODO
     */
    public function createOrUpdate(Review $review, User $user, array $tags): Review
    {
        $entityManager = $this->_em;

        $review->setTags($tags);
        $review->setAuthor($user);

        $review->setDateOfPublication(new DateTimeImmutable());

        $entityManager->persist($review);
        $entityManager->flush();

        return $review;
    }

    /**
     * @param Review $review
     *
     * @return void
     */
    public function update(Review $review): void
    {
        $this->_em->persist($review);
        $this->_em->flush();
    }

    /**
     * @param int $id
     *
     * @return Review|null
     * @TODO
     */
    public function findOneWithLikesAndTags(int $id): ?Review
    {
        return $this->createQueryBuilder('r')
            ->select('r, l')
            ->leftJoin('r.likes', 'l')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param Review $review
     *
     * @return void
     * @TODO
     */
    public function remove(Review $review): void
    {
        $em = $this->_em;

        foreach ($review->getTags() as $tag) {
            $review->removeTag($tag);
        }
        $em->flush();

        $em->remove($review);
        $em->flush();
    }

    /**
     * @return QueryBuilder
     */
    private function getMainQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->select('r as review, t as tags')
            ->addSelect('a.nickname as author_name, a.id as author_id')
            ->addSelect('coalesce(avg(rait.value), 0) as avgRating')
            ->addSelect('g.name as group')
            ->addSelect('count(arl) as author_likes')
            ->leftJoin('r.author', 'a')
            ->leftJoin('a.reviews', 'ar')
            ->leftJoin('ar.likes', 'arl')
            ->leftJoin('r.reviewRatings', 'rait')
            ->leftJoin('r.group', 'g')
            ->leftJoin('r.tags', 't')
            ->groupBy('r.id, a.id, g.id, t.id')
            ;
    }
}
