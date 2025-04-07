<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Persistence\Repository;

use App\Products\Domain\Model\ProductCategory;
use App\Products\Domain\Repository\ProductCategoryRepository;
use Ramsey\Uuid\UuidInterface;

final class InMemoryProductCategoryRepository implements ProductCategoryRepository
{
    private array $productCategories = [];

    public function save(ProductCategory $productCategory): void
    {
        $this->productCategories[$productCategory->getProductId()->toString()][$productCategory->getCategoryId()] = $productCategory;
    }

    public function delete(ProductCategory $productCategory): void
    {
        unset($this->productCategories[$productCategory->getProductId()->toString()][$productCategory->getCategoryId()]);
    }

    public function findAllById(UuidInterface $productId): array
    {
        return $this->productCategories[$productId->toString()] ?? [];
    }
}