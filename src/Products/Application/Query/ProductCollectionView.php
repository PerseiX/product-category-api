<?php

declare(strict_types=1);


namespace App\Products\Application\Query;

final class ProductCollectionView
{
    /** @param ProductView[] $productCollectionView */
    public function __construct(
        public readonly array $productCollectionView = []
    )
    {
    }
}