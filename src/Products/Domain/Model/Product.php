<?php

declare(strict_types=1);

namespace App\Products\Domain\Model;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
final class Product
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private UuidInterface $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Embedded(class: Money::class)]
    private Money $price;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public function __construct(UuidInterface $id, string $name, Money $price)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;

        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function assignCategory(int $categoryId): ProductCategory
    {
        return new ProductCategory($this->id, $categoryId);
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function updatePrice(Money $money): self
    {
        $this->price = $money;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }


    public function updateName(string $name): self
    {
        $this->name = $name;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
