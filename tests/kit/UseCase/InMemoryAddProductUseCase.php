<?php

declare(strict_types=1);

namespace App\Tests\kit\UseCase;

use App\Products\Application\UseCase\Create\AddProductCommand;
use App\Products\Application\UseCase\Create\AddProductInterface;
use App\Products\Application\UseCase\Create\Result;

final class InMemoryAddProductUseCase implements AddProductInterface
{
    private Result $result;

    public function withResult(Result $result): void
    {
        $this->result = $result;
    }

    public function execute(AddProductCommand $addProductCommand): Result
    {
        return $this->result;
    }
}
