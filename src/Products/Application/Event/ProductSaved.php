<?php

declare(strict_types=1);


namespace App\Products\Application\Event;

use Ramsey\Uuid\UuidInterface;

final class ProductSaved implements Event
{
    public function __construct(
        private(set) UuidInterface $productId,
        private(set) string $productName,
    )
    {
    }
}