<?php

declare(strict_types=1);

namespace App\Tests\kit\UseCase;

use App\Products\Application\UseCase\Update\Result;
use App\Products\Application\UseCase\Update\UpdateProductCommand;
use App\Products\Application\UseCase\Update\UpdateProductInterface;

final class InMemoryUpdateProductUseCase implements UpdateProductInterface
{

    private Result $result;

    public function withResult(Result $result): void
    {
        $this->result = $result;
    }

    public function execute(UpdateProductCommand $updateProductCommand): Result
    {
        return $this->result;
    }
}
