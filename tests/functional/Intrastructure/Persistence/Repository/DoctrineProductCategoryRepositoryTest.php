<?php

declare(strict_types=1);

namespace App\Tests\functional\Intrastructure\Persistence\Repository;

use App\Products\Application\Repository\ProductCategoryRepository;
use App\Products\Application\Repository\ProductRepository;
use App\Products\Domain\Model\Category;
use App\Products\Domain\Model\Money;
use App\Products\Domain\Model\Product;
use App\Products\Domain\Model\ProductCategory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineProductCategoryRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;
    private ProductCategoryRepository $productCategoryRepository;

    protected function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->productRepository = $container->get(ProductRepository::class);
        $this->productCategoryRepository = $container->get(ProductCategoryRepository::class);
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

        $this->entityManager->getConnection()->insert('category', [
            'id' => 2,
            'name' => 'Category 2',
            'created_at' => new DateTimeImmutable()->format('Y-m-d H:i:s'),
            'updated_at' => new DateTimeImmutable()->format('Y-m-d H:i:s'),
            'value_code' => '1222222222'
        ]);

        $productId = Uuid::uuid4();

        $this->productRepository->save(
            new Product($productId, 'Product 1', new Money('RON', 10, 22))
        );

        $this->productCategoryRepository->save(new ProductCategory($productId, 2));
        $productCategories = $this->productCategoryRepository->findAllById($productId);

        self::assertEquals(new ProductCategory($productId, 2), $productCategories[0]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->cleanUpDatabase();
    }

    private function cleanUpDatabase(): void
    {
        $this->entityManager->createQuery('DELETE FROM ' . ProductCategory::class)->execute();
        $this->entityManager->createQuery('DELETE FROM ' . Product::class)->execute();
        $this->entityManager->createQuery('DELETE FROM ' . Category::class)->execute();
    }
}
