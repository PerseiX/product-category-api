<?php

declare(strict_types=1);

namespace App\Tests\kit\UseCase;

use App\Products\Application\UseCase\Delete\DeleteProductCommand;
use App\Products\Application\UseCase\Delete\DeleteProductInterface;
use App\Products\Application\UseCase\Delete\Result;

final class InMemoryDeleteProductUseCase implements DeleteProductInterface
{
    private Result $result;

    public function withResult(Result $result): void
    {
        $this->result = $result;
    }

    public function execute(DeleteProductCommand $updateProductCommand): Result
    {
        return $this->result;
    }
}
