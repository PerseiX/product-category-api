<?php

declare(strict_types=1);

namespace App\Tests\kit\Query;

use App\Products\Application\Query\GetProductViewQuery;
use App\Products\Application\Query\ProductView;
use Ramsey\Uuid\UuidInterface;

final class InMemoryGetProductViewQuery implements GetProductViewQuery
{
    private ?ProductView $productView = null;

    public function withData(ProductView $productView)
    {
        $this->productView = $productView;
    }

    public function execute(UuidInterface $uuid): ?ProductView
    {
        return $this->productView;
    }
}
