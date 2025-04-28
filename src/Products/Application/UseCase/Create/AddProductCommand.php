<?php

declare(strict_types=1);


namespace App\Products\Application\UseCase\Create;

use Ramsey\Uuid\UuidInterface;

final class AddProductCommand
{
    public function __construct(
        public readonly string         $name,
        public readonly string         $price,
        public readonly string         $currency,
        public readonly array          $categories,
        public readonly ?UuidInterface $id = null
    ) {
    }
}
