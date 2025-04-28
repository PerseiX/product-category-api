<?php

declare(strict_types=1);


namespace App\Products\Application\UseCase\Delete;

use App\Products\Application\Repository\ProductCategoryRepository;
use App\Products\Application\Repository\ProductRepository;
use App\Products\Application\Services\TransactionManager;
use Throwable;

final class DeleteProduct implements DeleteProductInterface
{
    public function __construct(
        private readonly ProductRepository         $productRepository,
        private readonly ProductCategoryRepository $productCategoryRepository,
        private readonly TransactionManager        $transactionManager,
    ) {
    }

    public function execute(DeleteProductCommand $updateProductCommand): Result
    {
        $product = $this->productRepository->findById($updateProductCommand->id);
        if (null === $product) {
            return Result::productNotFound();
        }

        $this->productRepository->delete($product);

        try {
            $this->transactionManager->execute(function () use ($updateProductCommand) {
                $productCategories = $this->productCategoryRepository->findAllById($updateProductCommand->id);

                foreach ($productCategories as $productCategory) {
                    $this->productCategoryRepository->delete($productCategory);
                }
                return true;
            });
        } catch (Throwable) {
            return Result::unexpectedError();
        }
        return Result::success();
    }
}
