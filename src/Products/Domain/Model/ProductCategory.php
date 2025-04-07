<?php

declare(strict_types=1);

namespace App\Products\Domain\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
final class ProductCategory
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    private int $categoryId;

    #[ORM\Column(type: "uuid")]
    #[ORM\Id]
    private UuidInterface $productId;

    public function __construct(UuidInterface $productId, int $categoryId)
    {
        $this->productId = $productId;
        $this->categoryId = $categoryId;
    }

    public function getProductId(): UuidInterface
    {
        return $this->productId;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }
}