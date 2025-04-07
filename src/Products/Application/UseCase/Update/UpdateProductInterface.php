<?php

declare(strict_types=1);

namespace App\Products\Application\UseCase\Update;


interface UpdateProductInterface
{
    public function execute(UpdateProductCommand $updateProductCommand): Result;

}