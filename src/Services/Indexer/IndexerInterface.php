<?php

declare(strict_types=1);

namespace App\Services\Indexer;

/**
 * @template T
 */
interface IndexerInterface
{
    /**
     * @param T $element
     *
     * @return void
     */
    public function add(mixed $element): void;

    /**
     * @param T $element
     *
     * @return void
     */
    public function edit(mixed $element): void;

    /**
     * @param T $element
     *
     * @return void
     */
    public function remove(mixed $element): void;
}
