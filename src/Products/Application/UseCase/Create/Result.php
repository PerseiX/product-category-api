<?php

declare(strict_types=1);


namespace App\Products\Application\UseCase\Create;

use Ramsey\Uuid\UuidInterface;

final class Result
{
    private const CATEGORY_NOT_FOUND = 'category_not_found';
    private const CATEGORY_IS_REQUIRED = 'category_is_required';
    private const REST_OUT_OF_THE_RANGE = 'rest_out_of_the_range';
    private const WHOLE_NEGATIVE = 'whole_negative';
    private const UNEXPECTED_ERROR = 'unexpected_error';
    private const INVALID_PRICE = 'invalid_price';

    private bool $success;
    private ?string $reason;
    private ?UuidInterface $uuid;

    private function __construct(bool $success, ?string $reason = null, ?UuidInterface $uuid = null)
    {
        $this->success = $success;
        $this->reason = $reason;
        $this->uuid = $uuid;
    }

    public static function categoryNotFound(): self
    {
        return new self(false, self::CATEGORY_NOT_FOUND);
    }

    public static function unexpectedError(): self
    {
        return new self(false, self::UNEXPECTED_ERROR);
    }

    public static function restOutOfTheRange(): self
    {
        return new self(false, self::REST_OUT_OF_THE_RANGE);
    }

    public static function wholeNegative(): self
    {
        return new self(false, self::WHOLE_NEGATIVE);
    }

    public static function invalidPrice(): self
    {
        return new self(false, self::INVALID_PRICE);
    }

    public static function categoryIsRequired(): self
    {
        return new self(false, self::CATEGORY_IS_REQUIRED);
    }

    public static function success(UuidInterface $uuid): self
    {
        return new self(true, uuid: $uuid);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getUuid(): ?UuidInterface
    {
        return $this->uuid;
    }
}