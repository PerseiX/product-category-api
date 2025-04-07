<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Persistence\Repository;

use App\Products\Domain\Model\Product;
use App\Products\Domain\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

final class DoctrineProductRepository implements ProductRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function delete(Product $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    public function findById(UuidInterface $productId): ?Product
    {
        return $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $productId]);
    }
}