<?php

declare(strict_types=1);

namespace App\Repository\Review;

use App\Entity\Review\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[] findAll()
 * @method Tag[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @param array $tagsArrFromRequest
     *
     * @return array
     * @TODO
     */
    public function getEntityFromStringArray(array $tagsArrFromRequest): array
    {
        $tagsArr = [];
        foreach ($tagsArrFromRequest as $tag) {
            $tagsArr[] = $tag['name'];
        }
        $tagsEnt = $this->createQueryBuilder('t')
            ->where('t.name IN (:tag_names)')
            ->setParameter('tag_names', $tagsArr, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();
        foreach ($tagsEnt as $tag) {
            $key = array_search($tag->getName(), $tagsArr);
            unset($tagsArr[$key]);
        }
        foreach ($tagsArr as $tag) {
            $tagsEnt[] = new Tag($tag);
        }

        return $tagsEnt;
    }

    /**
     * @return Tag[]
     */
    public function findAllOrderByName(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $tag
     *
     * @return int
     */
    public function getLastReviewIdWithTag(string $tag): int
    {
        try {
            return $this->getQBForGetReviewsByTagName($tag)
                ->select('r.id')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (Exception) {
            return 0;
        }
    }

    /**
     * @param string|null $tagName
     *
     * @return QueryBuilder
     */
    private function getQBForGetReviewsByTagName(?string $tagName = null): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.reviews', 'r')
            ->where('t.name = :name')
            ->setParameter('name', $tagName)
            ->orderBy('r.id', 'DESC');
    }
}
