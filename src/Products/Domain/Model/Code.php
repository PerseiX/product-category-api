<?php

declare(strict_types=1);

namespace App\Products\Domain\Model;

use App\Products\Domain\Exception\CodeIsTooLong;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class Code
{
    #[ORM\Column(type: Types::STRING, length: 10, unique: true)]
    private string $code;

    public function __construct(string $code)
    {
        if (strlen($code) > 10) {
            throw new CodeIsTooLong();
        }
        $this->code = $code;
    }

    public function __toString(): string
    {
        return $this->code;
    }
}