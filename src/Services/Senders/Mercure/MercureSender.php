<?php

declare(strict_types=1);

namespace App\Services\Senders\Mercure;

use App\Services\Senders\SenderInterface;
use Symfony\Component\Mercure\HubInterface;

/**
 * @template T
 * @implements SenderInterface<T>
 */
abstract class MercureSender implements SenderInterface
{
    public function __construct(
        protected HubInterface $hub,
    ) {
    }

    /**
     * @param string $path
     * @param T $obj
     *
     * @return void
     */
    abstract public function publish(string $path, mixed $obj): void;

    /**
     * @param T $obj
     *
     * @return string
     */
    abstract protected function getMessage(mixed $obj): string;
}
