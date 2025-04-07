<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Persistence\Query;

use App\Products\Application\Query\GetAllProductsQuery;
use App\Products\Application\Query\ProductCollectionView;

final class InMemoryGetAllProductsQuery implements GetAllProductsQuery
{
    private ProductCollectionView $productCollectionView;

    public function __construct()
    {
        $this->productCollectionView = new ProductCollectionView([]);
    }

    public function withData(ProductCollectionView $collectionView)
    {
        $this->productCollectionView = $collectionView;
    }

    public function execute(): ProductCollectionView
    {
        return $this->productCollectionView;
    }
}