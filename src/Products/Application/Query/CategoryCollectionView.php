<?php

declare(strict_types=1);


namespace App\Products\Application\Query;

final class CategoryCollectionView
{
    /** @param CategoryView[] $categoryCollection */
    public function __construct(
        public readonly array $categoryCollection = []
    )
    {
    }
}