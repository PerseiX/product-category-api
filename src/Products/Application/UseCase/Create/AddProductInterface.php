<?php

declare(strict_types=1);


namespace App\Products\Application\UseCase\Create;

interface AddProductInterface
{
    public function execute(AddProductCommand $addProductCommand): Result;
}