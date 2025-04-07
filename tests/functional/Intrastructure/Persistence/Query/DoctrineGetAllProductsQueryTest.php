<?php

declare(strict_types=1);

namespace App\Tests\functional\Intrastructure\Persistence\Query;

use App\Products\Application\Query\CategoryCollectionView;
use App\Products\Application\Query\CategoryView;
use App\Products\Application\Query\GetAllProductsQuery;
use App\Products\Application\Query\ProductCollectionView;
use App\Products\Application\Query\ProductView;
use App\Products\Domain\Model\Category;
use App\Products\Domain\Model\Money;
use App\Products\Domain\Model\Product;
use App\Products\Domain\Model\ProductCategory;
use App\Products\Domain\Repository\ProductCategoryRepository;
use App\Products\Domain\Repository\ProductRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineGetAllProductsQueryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;
    private ProductCategoryRepository $productCategoryRepository;
    private GetAllProductsQuery $getAllProductsQuery;

    protected function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->productRepository = $container->get(ProductRepository::class);
        $this->productCategoryRepository = $container->get(ProductCategoryRepository::class);
        $this->getAllProductsQuery = $container->get(GetAllProductsQuery::class);
    }

    public function testFetchingWhenNothingFound(): void
    {
        $result = $this->productCategoryRepository->findAllById(Uuid::uuid4());
        self::assertEmpty($result);
    }

    public function testFindingAndDeleting(): void
    {
        $this->entityManager->getConnection()->beginTransaction();

        $this->entityManager->getConnection()->insert('category', [
            'id' => 1,
            'name' => 'Category 1',
            'created_at' => new DateTimeImmutable()->format('Y-m-d H:i:s'),
            'updated_at' => new DateTimeImmutable()->format('Y-m-d H:i:s'),
            'value_code' => '1234567890'
        ]);

        $category2Created = new DateTimeImmutable();
        $category2Updated = new DateTimeImmutable();

        $this->entityManager->getConnection()->insert('category', [
            'id' => 2,
            'name' => 'Category 2',
            'created_at' => $category2Created->format('Y-m-d H:i:s'),
            'updated_at' => $category2Updated->format('Y-m-d H:i:s'),
            'value_code' => '1222222222'
        ]);

        $productId = Uuid::uuid4();

        $this->productRepository->save(
            $product = new Product($productId, 'Product 1', new Money('RON', 10, 22))
        );
        $productCategory = $product->assignCategory(2);
        $this->productCategoryRepository->save($productCategory);

        $queryResponse = $this->getAllProductsQuery->execute();


        self::assertEquals(new ProductCollectionView(
            [
                new ProductView(
                    $productId,
                    'Product 1',
                    'RON',
                    '10.22',
                    $this->normalizeDateTime($product->getCreatedAt()),
                    $this->normalizeDateTime($product->getUpdatedAt()),
                    new CategoryCollectionView(
                        [
                            new CategoryView(
                                2,
                                '1222222222',
                                'Category 2',
                                $this->normalizeDateTime($category2Created),
                                $this->normalizeDateTime($category2Updated),

                            )
                        ]
                    )
                )
            ]
        ), $queryResponse);

    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->cleanUpDatabase();
    }

    function normalizeDateTime(DateTimeImmutable $dateTime): DateTimeImmutable
    {
        return $dateTime->setTime(
            (int)$dateTime->format('H'),
            (int)$dateTime->format('i'),
            (int)$dateTime->format('s'),
            0 // Set microseconds to zero
        );
    }

    private function cleanUpDatabase(): void
    {
        $this->entityManager->createQuery('DELETE FROM ' . ProductCategory::class)->execute();
        $this->entityManager->createQuery('DELETE FROM ' . Product::class)->execute();
        $this->entityManager->createQuery('DELETE FROM ' . Category::class)->execute();
    }
}