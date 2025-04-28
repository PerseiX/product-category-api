<?php

declare(strict_types=1);

namespace App\Products\Application\Repository;

interface CategoryRepository
{
    public function findIdByIds(int ...$categories): array;
}
