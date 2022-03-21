<?php

declare(strict_types=1);

namespace App\Services\Indexer\ElasticSearch;

use App\Entity\Review\Review;
use App\Services\Indexer\IndexerInterface;
use FOS\ElasticaBundle\Persister\ObjectPersister;

/**
 * @implements IndexerInterface<Review>
 */
class ESIndexer implements IndexerInterface
{
    public function __construct(
        private ObjectPersister $index,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function add(mixed $element): void
    {
        $this->index->insertOne($element);
    }

    /**
     * {@inheritdoc}
     */
    public function edit(mixed $element): void
    {
        $this->index->replaceOne($element);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(mixed $element): void
    {
        $this->index->deleteOne($element);
    }
}
