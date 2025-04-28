<?php

declare(strict_types=1);


namespace App\Products\Application\UseCase\Delete;

use Ramsey\Uuid\UuidInterface;

final class DeleteProductCommand
{
    public function __construct(
        public readonly UuidInterface $id,
    ) {
    }
}
