<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Persistence\Repository;

use App\Products\Application\Repository\ProductCategoryRepository;
use App\Products\Domain\Model\ProductCategory;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

final class DoctrineProductCategoryRepository implements ProductCategoryRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(ProductCategory $productCategory): void
    {
        $this->entityManager->persist($productCategory);
        $this->entityManager->flush();
    }

    public function delete(ProductCategory $productCategory): void
    {
        $this->entityManager->remove($productCategory);
        $this->entityManager->flush();
    }

    public function findAllById(UuidInterface $productId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('pc')
            ->from(ProductCategory::class, 'pc')
            ->andWhere('pc.productId IN (:productId)')
            ->setParameter('productId', $productId->toString())
            ->getQuery()
            ->getResult();
    }
}
