<?php

declare(strict_types=1);

namespace App\Tests\unit\Application\UseCase\Update;

use App\Products\Application\Event\EventPublisher;
use App\Products\Application\Event\ProductSaved;
use App\Products\Application\Services\TransactionManager;
use App\Products\Application\UseCase\Update\Result;
use App\Products\Application\UseCase\Update\UpdateProduct;
use App\Products\Application\UseCase\Update\UpdateProductCommand;
use App\Products\Application\UseCase\Update\UpdateProductInterface;
use App\Products\Domain\Model\Category;
use App\Products\Domain\Model\Money;
use App\Products\Domain\Model\Product;
use App\Products\Domain\Repository\CategoryRepository;
use App\Products\Domain\Repository\ProductCategoryRepository;
use App\Products\Domain\Repository\ProductRepository;
use App\Products\Infrastructure\EventPublisher\InMemoryEventPublisher;
use App\Products\Infrastructure\Persistence\Repository\InMemoryCategoryRepository;
use App\Products\Infrastructure\Persistence\Repository\InMemoryProductCategoryRepository;
use App\Products\Infrastructure\Persistence\Repository\InMemoryProductRepository;
use App\Tests\kit\InMemoryTransactionManager;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class UpdateProductTest extends TestCase
{
    private UpdateProductInterface $updateProduct;
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private ProductCategoryRepository $productCategoryRepository;
    private TransactionManager $transactionManager;
    private EventPublisher $eventPublisher;

    protected function setUp(): void
    {
        $this->productRepository = new InMemoryProductRepository();
        $this->categoryRepository = new InMemoryCategoryRepository();
        $this->productCategoryRepository = new InMemoryProductCategoryRepository();
        $this->transactionManager = new InMemoryTransactionManager();
        $this->eventPublisher = new InMemoryEventPublisher();

        $this->updateProduct = new UpdateProduct(
            $this->categoryRepository,
            $this->productRepository,
            $this->productCategoryRepository,
            $this->transactionManager,
            $this->eventPublisher
        );
    }

    public function testCategoryIsRequired(): void
    {
        self::assertEquals(
            Result::categoryIsRequired(),
            $this->updateProduct->execute(
                new UpdateProductCommand(
                    Uuid::uuid4(),
                    'Product 1',
                    [],
                    '10.2',
                    'PLN',
                )
            )
        );
    }

    public function testCategoryNotFound(): void
    {
        self::assertEquals(
            Result::categoryNotFound(),
            $this->updateProduct->execute(
                new UpdateProductCommand(
                    Uuid::uuid4(),
                    'Product 1',
                    [1],
                    '10.2',
                    'PLN',
                )
            )
        );
    }

    public function testProductNotFound(): void
    {
        $this->categoryRepository->save(
            new Category(
                1,
                '1234567890',
                'Category 1',
                new DateTimeImmutable()
            )
        );

        self::assertEquals(
            Result::productNotFound(),
            $this->updateProduct->execute(
                new UpdateProductCommand(
                    Uuid::uuid4(),
                    'Product 1',
                    [1],
                    '10.2',
                    'PLN',
                )
            )
        );
    }

    public function testProductUpdate(): void
    {
        $this->categoryRepository->save(
            new Category(
                1,
                '1234567890',
                'Category 1',
                new DateTimeImmutable()
            )
        );

        $productId = Uuid::uuid4();
        $this->productRepository->save(
            new Product(
                $productId,
                'Product 1',
                new Money(
                    'PLN',
                    100,
                    20
                )
            )
        );

        $result = $this->updateProduct->execute(
            new UpdateProductCommand(
                $productId,
                'Product 55',
                [1],
                '4.2',
                'PLN',
            )
        );

        self::assertTrue($result->isSuccess());
        $product = $this->productRepository->findById($productId);

        self::assertEquals($product->getName(), 'Product 55');
        self::assertEquals($product->getPrice(), new Money('PLN', 4, 2));
        self::assertCount(1, $this->eventPublisher->events);
        self::assertEquals(new ProductSaved($product->getId(), $product->getName()), $this->eventPublisher->events[0]);
    }

    public function testTransactionError(): void
    {
        $this->categoryRepository->save(
            new Category(
                1,
                '1234567890',
                'Category 1',
                new DateTimeImmutable()
            )
        );

        $productId = Uuid::uuid4();
        $this->productRepository->save(
            new Product(
                $productId,
                'Product 1',
                new Money(
                    'PLN',
                    100,
                    20
                )
            )
        );
        $this->transactionManager->forceException = true;

        $result = $this->updateProduct->execute(
            new UpdateProductCommand(
                $productId,
                'Product 55',
                [1],
                '4.2',
                'PLN',
            )
        );

        self::assertEquals($result, Result::unexpectedError());
        self::assertCount(0, $this->eventPublisher->events);
    }

    public function testWholeMoneyNegative(): void
    {
        $this->categoryRepository->save(
            new Category(
                1,
                '1234567890',
                'Category 1',
                new DateTimeImmutable()
            )
        );

        $productId = Uuid::uuid4();
        $this->productRepository->save(
            new Product(
                $productId,
                'Product 1',
                new Money(
                    'PLN',
                    100,
                    20
                )
            )
        );
        $this->transactionManager->forceException = true;

        $result = $this->updateProduct->execute(
            new UpdateProductCommand(
                $productId,
                'Product 55',
                [1],
                '-4.2',
                'PLN',
            )
        );

        self::assertEquals($result, Result::wholeNegative());
    }

    public function testRestOutOfTheRange(): void
    {
        $this->categoryRepository->save(
            new Category(
                1,
                '1234567890',
                'Category 1',
                new DateTimeImmutable()
            )
        );

        $productId = Uuid::uuid4();
        $this->productRepository->save(
            new Product(
                $productId,
                'Product 1',
                new Money(
                    'PLN',
                    100,
                    20
                )
            )
        );
        $this->transactionManager->forceException = true;

        $result = $this->updateProduct->execute(
            new UpdateProductCommand(
                $productId,
                'Product 55',
                [1],
                '4.2123',
                'PLN',
            )
        );

        self::assertEquals($result, Result::restOutOfTheRange());
    }
}