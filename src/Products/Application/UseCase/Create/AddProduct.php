<?php

declare(strict_types=1);


namespace App\Products\Application\UseCase\Create;

use App\Products\Application\Event\EventPublisher;
use App\Products\Application\Event\ProductSaved;
use App\Products\Application\Services\TransactionManager;
use App\Products\Domain\Exception\InvalidPrice;
use App\Products\Domain\Exception\RestOutOfTheRange;
use App\Products\Domain\Exception\WholeNegative;
use App\Products\Domain\Model\Money;
use App\Products\Domain\Model\Product;
use App\Products\Domain\Repository\CategoryRepository;
use App\Products\Domain\Repository\ProductCategoryRepository;
use App\Products\Domain\Repository\ProductRepository;
use Ramsey\Uuid\Uuid;
use Throwable;

final class AddProduct implements AddProductInterface
{
    public function __construct(
        private readonly CategoryRepository        $categoryRepository,
        private readonly ProductRepository         $productRepository,
        private readonly ProductCategoryRepository $productCategoryRepository,
        private readonly TransactionManager        $transactionManager,
        private readonly EventPublisher            $eventPublisher,
    )
    {
    }

    public function execute(AddProductCommand $addProductCommand): Result
    {
        if ([] === $addProductCommand->categories) {
            return Result::categoryIsRequired();
        }

        $categories = $this->categoryRepository->findIdByIds(...$addProductCommand->categories);

        if (count($categories) !== count($addProductCommand->categories)) {
            return Result::categoryNotFound();
        }

        try {
            $productId = $addProductCommand->id ?? Uuid::uuid4();

            $product = new Product(
                $productId,
                $addProductCommand->name,
                Money::create(
                    $addProductCommand->currency,
                    $addProductCommand->price,
                )
            );
        } catch (RestOutOfTheRange $e) {
            return Result::restOutOfTheRange();
        } catch (WholeNegative $e) {
            return Result::wholeNegative();
        } catch (InvalidPrice $e) {
            return Result::invalidPrice();
        }

        try {
            $this->transactionManager->execute(function () use ($product, $categories) {
                foreach ($categories as $category) {
                    $this->productCategoryRepository->save($product->assignCategory($category));
                }

                $this->productRepository->save($product);
                $this->eventPublisher->publish(new ProductSaved($product->getId(), $product->getName()));

                return true;
            });
        } catch (Throwable $e) {
            return Result::unexpectedError();
        }

        return Result::success($productId);
    }
}