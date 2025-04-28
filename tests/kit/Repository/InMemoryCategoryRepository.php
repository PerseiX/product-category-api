<?php

declare(strict_types=1);

namespace App\Tests\kit\Repository;

use App\Products\Application\Repository\CategoryRepository;
use App\Products\Domain\Model\Category;

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
