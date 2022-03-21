<?php

declare(strict_types=1);

namespace App\Repository\Review;

use App\Entity\Review\Rating;
use App\Entity\Users\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rating|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rating|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rating[] findAll()
 * @method Rating[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    /**
     * @param User $user
     * @param int $reviewId
     *
     * @return Rating|null
     *
     * @throws NonUniqueResultException
     */
    public function findOneWithReview(User $user, int $reviewId): ?Rating
    {
        $expr = $this->_em->getExpressionBuilder();

        return $this->createQueryBuilder('r')
            ->where(
                $expr->andX(
                    'r.valuer = :user',
                    'rr.id = :review',
                )
            )
            ->leftJoin('r.review', 'rr')
            ->setParameter('user', $user)
            ->setParameter('review', $reviewId)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param Rating $rating
     *
     * @return void
     */
    public function add(Rating $rating): void
    {
        $this->_em->persist($rating);
        $this->_em->flush();
    }

    /**
     * @param Rating $rating
     *
     * @return void
     */
    public function remove(Rating $rating): void
    {
        $this->_em->remove($rating);
        $this->_em->flush();
    }
}
