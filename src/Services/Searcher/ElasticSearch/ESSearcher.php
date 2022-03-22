<?php

declare(strict_types=1);

namespace App\Services\Searcher\ElasticSearch;

use App\Entity\Review\Review;
use App\Services\Searcher\SearcherInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\HybridResult;
use Pagerfanta\Pagerfanta;

class ESSearcher implements SearcherInterface
{
    public function __construct(
        private PaginatedFinderInterface $finder,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function getResultsCount(string $query): int
    {
        return $this->finder
            ->findPaginated($query)
            ->count();
    }

    /**
     * @inheritDoc
     */
    public function getResultsByPage(string $query, int $page, int $rows = 10): array
    {
        $paginator = $this->finder->findPaginated($query);
        $paginator->setMaxPerPage($rows);
        if ($page > $paginator->getNbPages()) {
            return [];
        }
        $paginator->setCurrentPage($page);

        return $this->setResult($paginator);
    }

    /**
     * @param Pagerfanta $paginator
     * @return array
     */
    private function setResult(Pagerfanta $paginator) : array
    {
        $result = [];

        /** @var HybridResult $item */
        foreach($paginator as $item) {
            $result[] = $item;
        }

        return $result;
    }
}