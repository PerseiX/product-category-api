<?php

declare(strict_types=1);

namespace App\Tests\kit\Repository;

use App\Products\Application\Repository\ProductCategoryRepository;
use App\Products\Domain\Model\ProductCategory;
use Ramsey\Uuid\UuidInterface;

final class InMemoryProductCategoryRepository implements ProductCategoryRepository
{
    private array $productCategories = [];

    public function save(ProductCategory $productCategory): void
    {
        $this->productCategories[$productCategory->getProductId()->toString()][$productCategory->getCategoryId()]
            = $productCategory;
    }

    public function delete(ProductCategory $productCategory): void
    {
        unset(
            $this->productCategories[$productCategory->getProductId()->toString()][$productCategory->getCategoryId()]
        );
    }

    public function findAllById(UuidInterface $productId): array
    {
        return $this->productCategories[$productId->toString()] ?? [];
    }
}
