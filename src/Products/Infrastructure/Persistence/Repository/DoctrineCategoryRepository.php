<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Persistence\Repository;

use App\Products\Application\Repository\CategoryRepository;
use App\Products\Domain\Model\Category;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineCategoryRepository implements CategoryRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Category $category)
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function findIdByIds(int ...$categories): array
    {
        $categoriesFromDb = $this->entityManager->createQueryBuilder()
            ->select('c.id')
            ->from(Category::class, 'c')
            ->andWhere('c.id IN (:categoryIds)')
            ->setParameter('categoryIds', $categories)
            ->getQuery()
            ->getArrayResult();

        $result = [];
        foreach ($categoriesFromDb as $category) {
            $result[] = $category['id'];
        }

        return $result;
    }
}
