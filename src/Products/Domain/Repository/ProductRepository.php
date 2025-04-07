<?php

declare(strict_types=1);

namespace App\Products\Domain\Repository;

use App\Products\Domain\Model\Product;
use Ramsey\Uuid\UuidInterface;

interface ProductRepository
{
    public function save(Product $product): void;

    public function delete(Product $product): void;

    public function findById(UuidInterface $productId): ?Product;
}