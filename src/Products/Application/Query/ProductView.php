<?php

declare(strict_types=1);


namespace App\Products\Application\Query;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final class ProductView
{
    public function __construct(
        public readonly UuidInterface          $productId,
        public readonly string                 $name,
        public readonly string                 $currency,
        public readonly string                 $price,
        public readonly DateTimeImmutable      $createdAt,
        public readonly DateTimeImmutable      $updatedAt,
        public readonly CategoryCollectionView $categoryCollection
    )
    {

    }
}