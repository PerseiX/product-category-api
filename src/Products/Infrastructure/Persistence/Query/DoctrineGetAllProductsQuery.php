<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Persistence\Query;

use App\Products\Application\Query\CategoryCollectionView;
use App\Products\Application\Query\CategoryView;
use App\Products\Application\Query\GetAllProductsQuery;
use App\Products\Application\Query\ProductCollectionView;
use App\Products\Application\Query\ProductView;
use App\Products\Domain\Model\Category;
use App\Products\Domain\Model\Product;
use App\Products\Domain\Model\ProductCategory;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineGetAllProductsQuery implements GetAllProductsQuery
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function execute(): ProductCollectionView
    {
        $products = $this->entityManager->createQueryBuilder()
            ->select('p.id',
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
            ->getQuery()
            ->getResult();

        if (null === $products) {
            return new ProductCollectionView();
        }

        $groupedByProductId = [];

        foreach ($products as $product) {
            $groupedByProductId[$product['id']->toString()][] = $product;
        }

        $productViews = [];
        foreach ($groupedByProductId as $product) {
            $categoryCollection = [];

            foreach ($product as $item) {
                if (null !== $item['categoryId']) {
                    $categoryCollection[] =
                        new CategoryView(
                            $item['categoryId'],
                            $item['categoryCode'],
                            $item['categoryName'],
                            $item['categoryCreatedAt'],
                            $item['categoryUpdatedAt'],
                        );
                }
            }

            $product = $product[0];
            $productViews[] = new ProductView(
                $product['id'],
                $product['name'],
                $product['price.currency'],
                $product['price.whole'] . '.' . $product['price.rest'],
                $product['createdAt'],
                $product['updatedAt'],
                new CategoryCollectionView($categoryCollection)
            );
        }

        return new ProductCollectionView($productViews);
    }
}