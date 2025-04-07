<?php

declare(strict_types=1);

namespace App\Products\Domain\Model;

use App\Products\Domain\Exception\InvalidPrice;
use App\Products\Domain\Exception\RestOutOfTheRange;
use App\Products\Domain\Exception\WholeNegative;
use App\Products\Domain\ValueObject\Column;
use App\Products\Domain\ValueObject\Embeddable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class Money
{
    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'integer')]
    private int $whole;

    #[ORM\Column(type: 'integer')]
    private int $rest;

    public function __construct(
        string $currency,
        int    $whole,
        int    $rest
    )
    {
        if ($whole < 0) {
            throw new WholeNegative();
        }

        if ($rest < 0 || $rest > 99) {
            throw new RestOutOfTheRange();
        }

        $this->currency = $currency;
        $this->whole = $whole;
        $this->rest = $rest;
    }

    public static function create(string $currency, string $price): self
    {
        if (!is_numeric($price)) {
            throw new InvalidPrice();
        }
        $price = explode('.', $price);

        $whole = (int)$price[0];
        $rest = isset($price[1]) ? (int)$price[1] : 0;

        return new self($currency, $whole, $rest);
    }
}