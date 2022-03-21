<?php

declare(strict_types=1);

namespace App\Repository\Review;

use App\Entity\Review\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException as NonUniqueResultExceptionAlias;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[] findAll()
 * @method Comment[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @return int
     */
    public function getLastId(): int
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('c.id')
                ->orderBy('c.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (Exception) {
            return 0;
        }
    }

    /**
     * @param int $lastId
     * @param int $reviewId
     *
     * @return Query
     */
    public function getQueryForPageWithComment(int $lastId, int $reviewId): Query
    {
        $expr = $this->_em->getExpressionBuilder();

        return $this->createQueryBuilder('c')
            ->select('c as comment')
            ->addSelect('a.nickname as author_name, a.id as author_id')
            ->addSelect('count(arl) as author_likes')
            ->groupBy('c.id, a.id')
            ->leftJoin('c.author', 'a')
            ->leftJoin('a.reviews', 'ar')
            ->leftJoin('ar.likes', 'arl')
            ->leftJoin('c.review', 'r')
            ->where(
                $expr->andX(
                    $expr->lte('c.id', ':lastId'),
                    $expr->eq('r.id', ':reviewId'),
                )
            )
            ->orderBy('c.id', 'desc')
            ->setParameter('lastId', $lastId)
            ->setParameter('reviewId', $reviewId)
            ->getQuery();
    }

    /**
     * @param Comment $comment
     *
     * @return Comment
     */
    public function addComment(Comment $comment): Comment
    {
        $this->_em->persist($comment);
        $this->_em->flush();

        return $comment;
    }

    /**
     * @param int $id
     *
     * @return array{comment: Comment, author_id: int}|null
     *
     * @throws NonUniqueResultExceptionAlias
     */
    public function findByIdWithAuthorId(int $id): ?array
    {
        return $this->createQueryBuilder('c')
            ->select('c as comment, a.id as author_id, cr')
            ->leftJoin('c.author', 'a')
            ->leftJoin('c.review', 'cr')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param Comment $comment
     *
     * @return void
     */
    public function remove(Comment $comment): void
    {
        $this->_em->remove($comment);
        $this->_em->flush();
    }
}
