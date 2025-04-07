<?php

declare(strict_types=1);

namespace App\Products\Domain\Model;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class Category
{
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    private int $id;

    #[ORM\Embedded(class: Code::class, columnPrefix: 'value_')]
    private Code $value;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public function __construct(int $id, string $value, string $name, DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->value = new Code($value);
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }
}