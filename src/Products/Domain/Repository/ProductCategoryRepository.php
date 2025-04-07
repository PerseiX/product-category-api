<?php

declare(strict_types=1);

namespace App\Products\Domain\Repository;

use App\Products\Domain\Model\ProductCategory;
use Ramsey\Uuid\UuidInterface;

interface ProductCategoryRepository
{
    public function save(ProductCategory $productCategory): void;

    public function delete(ProductCategory $productCategory): void;

    public function findAllById(UuidInterface $productId): array;

}