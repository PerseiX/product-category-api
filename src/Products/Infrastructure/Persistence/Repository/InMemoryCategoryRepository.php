<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Persistence\Repository;

use App\Products\Domain\Model\Category;
use App\Products\Domain\Repository\CategoryRepository;

final class InMemoryCategoryRepository implements CategoryRepository
{
    private array $categories = [];

    public function save(Category $category): void
    {
        $this->categories[$category->getId()] = $category;
    }

    public function findIdByIds(int ...$categories): array
    {
        $result = [];
        foreach ($categories as $id) {
            if (isset($this->categories[$id])) {
                $result[] = $id;
            }
        }

        return $result;
    }
}