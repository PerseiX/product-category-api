<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Persistence\Repository;

use App\Products\Domain\Model\Product;
use App\Products\Domain\Repository\ProductRepository;
use Ramsey\Uuid\UuidInterface;

final class InMemoryProductRepository implements ProductRepository
{
    private array $products = [];

    public function save(Product $product): void
    {
        $this->products[$product->getId()->toString()] = $product;
    }

    public function delete(Product $product): void
    {
        unset($this->products[$product->getId()->toString()]);
    }

    public function findById(UuidInterface $productId): ?Product
    {
        return $this->products[$productId->toString()] ?? null;
    }
}