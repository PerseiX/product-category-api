<?php

declare(strict_types=1);


namespace App\Products\Application\UseCase\Delete;


interface DeleteProductInterface
{
    public function execute(DeleteProductCommand $updateProductCommand): Result;

}