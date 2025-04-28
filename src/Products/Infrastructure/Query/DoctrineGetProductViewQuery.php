<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Query;

use App\Products\Application\Query\CategoryCollectionView;
use App\Products\Application\Query\CategoryView;
use App\Products\Application\Query\GetProductViewQuery;
use App\Products\Application\Query\ProductView;
use App\Products\Domain\Model\Category;
use App\Products\Domain\Model\Product;
use App\Products\Domain\Model\ProductCategory;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

final class DoctrineGetProductViewQuery implements GetProductViewQuery
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function execute(UuidInterface $uuid): ?ProductView
    {
        $products = $this->entityManager->createQueryBuilder()
            ->select(
                'p.id',
                'p.name',
                'p.createdAt',
                'p.updatedAt',
                'p.price.currency',
                'p.price.whole',
                'p.price.rest',
                'c.name as categoryName',
                'c.id as categoryId',
                'c.value.code as categoryCode',
                'c.createdAt as categoryCreatedAt',
                'c.updatedAt as categoryUpdatedAt',
            )
            ->from(Product::class, 'p')
            ->leftJoin(ProductCategory::class, 'pc', 'WITH', 'pc.productId = p.id')
            ->leftJoin(Category::class, 'c', 'WITH', 'pc.categoryId = c.id')
            ->setParameter('productId', $uuid->toString())
            ->andWhere('p.id = :productId')
            ->getQuery()
            ->getResult();

        if ([] === $products) {
            return null;
        }
        $categoryCollection = [];

        foreach ($products as $product) {
            $categoryCollection = [];
            if (null !== $product['categoryId']) {
                $categoryCollection[] =
                    new CategoryView(
                        $product['categoryId'],
                        $product['categoryCode'],
                        $product['categoryName'],
                        $product['categoryCreatedAt'],
                        $product['categoryUpdatedAt'],
                    );
            }
        }

        $product = $products[0];

        return new ProductView(
            $product['id'],
            $product['name'],
            $product['price.currency'],
            $product['price.whole'] . '.' . $product['price.rest'],
            $product['createdAt'],
            $product['updatedAt'],
            new CategoryCollectionView($categoryCollection)
        );
    }
}
