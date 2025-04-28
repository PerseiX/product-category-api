<?php

declare(strict_types=1);

namespace App\Products\Application\UseCase\Update;

use Ramsey\Uuid\UuidInterface;

final class UpdateProductCommand
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string        $name,
        public readonly array         $categories,
        public readonly string        $price,
        public readonly string        $currency,
    ) {
    }
}
