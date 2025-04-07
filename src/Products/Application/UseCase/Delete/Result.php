<?php

declare(strict_types=1);


namespace App\Products\Application\UseCase\Delete;

final class Result
{
    private const PRODUCT_NOT_FOUND = 'product_not_found';
    private const UNEXPECTED_ERROR = 'unexpected_error';

    private bool $success;
    private ?string $reason = null;

    private function __construct(bool $success, ?string $reason = null)
    {
        $this->success = $success;
        $this->reason = $reason;
    }

    public static function productNotFound(): self
    {
        return new self(false, self::PRODUCT_NOT_FOUND);
    }

    public static function success(): self
    {
        return new self(true);
    }

    public static function unexpectedError(): self
    {
        return new self(false, self::UNEXPECTED_ERROR);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }
}