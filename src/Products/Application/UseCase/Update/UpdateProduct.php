<?php

declare(strict_types=1);


namespace App\Products\Application\UseCase\Update;

use App\Products\Application\Event\EventPublisher;
use App\Products\Application\Event\ProductSaved;
use App\Products\Application\Repository\CategoryRepository;
use App\Products\Application\Repository\ProductCategoryRepository;
use App\Products\Application\Repository\ProductRepository;
use App\Products\Application\Services\TransactionManager;
use App\Products\Domain\Exception\InvalidPrice;
use App\Products\Domain\Exception\RestOutOfTheRange;
use App\Products\Domain\Exception\WholeNegative;
use App\Products\Domain\Model\Money;
use Throwable;

final class UpdateProduct implements UpdateProductInterface
{
    public function __construct(
        private readonly CategoryRepository        $categoryRepository,
        private readonly ProductRepository         $productRepository,
        private readonly ProductCategoryRepository $productCategoryRepository,
        private readonly TransactionManager        $transactionManager,
        private readonly EventPublisher            $eventPublisher,
    ) {
    }

    public function execute(UpdateProductCommand $updateProductCommand): Result
    {
        if ([] === $updateProductCommand->categories) {
            return Result::categoryIsRequired();
        }

        $categories = $this->categoryRepository->findIdByIds(...$updateProductCommand->categories);

        if (count($categories) !== count($updateProductCommand->categories)) {
            return Result::categoryNotFound();
        }

        $product = $this->productRepository->findById($updateProductCommand->id);

        if (null === $product) {
            return Result::productNotFound();
        }

        try {
            $product
                ->updateName($updateProductCommand->name)
                ->updatePrice(
                    Money::create(
                        $updateProductCommand->currency,
                        $updateProductCommand->price,
                    ),
                );
        } catch (RestOutOfTheRange $e) {
            return Result::restOutOfTheRange();
        } catch (WholeNegative $e) {
            return Result::wholeNegative();
        } catch (InvalidPrice $e) {
            return Result::invalidPrice();
        }

        $oldProductCategories = $this->productCategoryRepository->findAllById($product->getId());


        try {
            $this->transactionManager->execute(function () use ($product, $categories, $oldProductCategories) {
                foreach ($oldProductCategories as $productCategory) {
                    $this->productCategoryRepository->delete($productCategory);
                }

                foreach ($categories as $category) {
                    $this->productCategoryRepository->save($product->assignCategory($category));
                }

                $this->productRepository->save($product);

                $this->eventPublisher->publish(new ProductSaved($product->getId(), $product->getName()));
                return true;
            });
        } catch (Throwable) {
            return Result::unexpectedError();
        }
        return Result::success();
    }
}
