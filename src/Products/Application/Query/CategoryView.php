<?php

declare(strict_types=1);


namespace App\Products\Application\Query;

use DateTimeImmutable;

final class CategoryView
{
    public function __construct(
        public readonly int               $categoryId,
        public readonly string            $code,
        public readonly string            $name,
        public readonly DateTimeImmutable $createdAt,
        public readonly DateTimeImmutable $updatedAt
    ) {
    }
}
