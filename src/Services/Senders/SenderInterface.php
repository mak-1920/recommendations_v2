<?php

declare(strict_types=1);

namespace App\Services\Senders;

/**
 * @template T
 */
interface SenderInterface
{
    /**
     * @param string $path
     * @param T $obj
     *
     * @return void
     */
    public function publish(string $path, mixed $obj): void;
}
