<?php

declare(strict_types=1);

namespace App\Tests\functional\Intrastructure\Persistence\Repository;

use App\Products\Application\Repository\CategoryRepository;
use App\Products\Domain\Model\Category;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineCategoryRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private CategoryRepository $categoryRepository;

    protected function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->categoryRepository = $container->get(CategoryRepository::class);
    }

    public function testFetchingWhenNothingFound(): void
    {
        $result = $this->categoryRepository->findIdByIds(123);
        self::assertEmpty($result);
    }

    public function testFinding(): void
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

        $result = $this->categoryRepository->findIdByIds(2);

        self::assertEquals(2, $result[0]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->cleanUpDatabase();
    }

    private function cleanUpDatabase(): void
    {
        $this->entityManager->createQuery('DELETE FROM ' . Category::class)->execute();
    }
}
