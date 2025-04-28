<?php

declare(strict_types=1);

namespace App\Tests\unit\Application\UseCase\Create;

use App\Products\Application\Event\EventPublisher;
use App\Products\Application\Repository\CategoryRepository;
use App\Products\Application\Repository\ProductCategoryRepository;
use App\Products\Application\Repository\ProductRepository;
use App\Products\Application\Services\TransactionManager;
use App\Products\Application\UseCase\Create\AddProduct;
use App\Products\Application\UseCase\Create\AddProductCommand;
use App\Products\Application\UseCase\Create\AddProductInterface;
use App\Products\Application\UseCase\Create\Result;
use App\Products\Domain\Model\Category;
use App\Products\Infrastructure\EventPublisher\InMemoryEventPublisher;
use App\Tests\kit\Repository\InMemoryCategoryRepository;
use App\Tests\kit\Repository\InMemoryProductCategoryRepository;
use App\Tests\kit\Repository\InMemoryProductRepository;
use App\Tests\kit\UseCase\InMemoryTransactionManager;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class AddProductTest extends TestCase
{
    private AddProductInterface $addProduct;
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

        $this->addProduct = new AddProduct(
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
            $this->addProduct->execute(
                new AddProductCommand(
                    name: 'Product 1',
                    price: '10.2',
                    currency: 'PLN',
                    categories: [],
                    id: null
                )
            )
        );
    }

    public function testCategoryNotFound(): void
    {
        self::assertEquals(
            Result::categoryNotFound(),
            $this->addProduct->execute(
                new AddProductCommand(
                    name: 'Product 1',
                    price: '10.2',
                    currency: 'PLN',
                    categories: [1],
                    id: null
                )
            )
        );
    }

    public function testAddProduct(): void
    {
        $this->categoryRepository->save(
            new Category(
                1,
                '1234567890',
                'Category 1',
                new DateTimeImmutable()
            )
        );
        $result = $this->addProduct->execute(
            new AddProductCommand(
                name: 'Product 1',
                price: '10.2',
                currency: 'PLN',
                categories: [1],
                id: null
            )
        );

        self::assertEquals($result->getUuid()->toString(), $this->eventPublisher->events[0]->productId);
        self::assertTrue($result->isSuccess());
        self::assertEquals($this->productRepository->findById($result->getUuid())->getName(), 'Product 1');
    }

    public function testUnexpectedError(): void
    {
        $this->categoryRepository->save(
            new Category(
                1,
                '1234567890',
                'Category 1',
                new DateTimeImmutable()
            )
        );
        $this->transactionManager->forceException = true;
        $result = $this->addProduct->execute(
            new AddProductCommand(
                name: 'Product 1',
                price: '10.2',
                currency: 'PLN',
                categories: [1],
                id: null
            )
        );

        self::assertEquals($result, Result::unexpectedError());
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
        $result = $this->addProduct->execute(
            new AddProductCommand(
                name: 'Product 1',
                price: '-10.2',
                currency: 'PLN',
                categories: [1],
                id: null
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
        $result = $this->addProduct->execute(
            new AddProductCommand(
                name: 'Product 1',
                price: '10.132',
                currency: 'PLN',
                categories: [1],
                id: null
            )
        );

        self::assertEquals($result, Result::restOutOfTheRange());
    }
}
