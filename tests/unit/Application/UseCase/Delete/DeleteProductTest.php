<?php

declare(strict_types=1);

namespace App\Tests\unit\Application\UseCase\Delete;

use App\Products\Application\Repository\ProductRepository;
use App\Products\Application\Services\TransactionManager;
use App\Products\Application\UseCase\Delete\DeleteProduct;
use App\Products\Application\UseCase\Delete\DeleteProductCommand;
use App\Products\Application\UseCase\Delete\DeleteProductInterface;
use App\Products\Application\UseCase\Delete\Result;
use App\Products\Domain\Model\Money;
use App\Products\Domain\Model\Product;
use App\Tests\kit\Repository\InMemoryProductCategoryRepository;
use App\Tests\kit\Repository\InMemoryProductRepository;
use App\Tests\kit\UseCase\InMemoryTransactionManager;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class DeleteProductTest extends TestCase
{
    private DeleteProductInterface $deleteProduct;
    private ProductRepository $productRepository;
    private TransactionManager $transactionManager;

    public function testProductNotFound(): void
    {
        $uuid = Uuid::uuid4();

        self::assertEquals(
            Result::productNotFound(),
            $this->deleteProduct->execute(
                new DeleteProductCommand($uuid)
            )
        );
    }

    public function testProductExist(): void
    {
        $uuid = Uuid::uuid4();

        $this->productRepository->save(
            new Product(
                $uuid,
                'Product 1',
                new Money(
                    'PLN',
                    100,
                    20
                ),
            )
        );
        self::assertEquals(
            Result::success(),
            $this->deleteProduct->execute(
                new DeleteProductCommand($uuid)
            )
        );
        self::assertEquals(
            Result::productNotFound(),
            $this->deleteProduct->execute(
                new DeleteProductCommand($uuid)
            )
        );
    }

    public function testUnexpectedException(): void
    {
        $uuid = Uuid::uuid4();

        $this->productRepository->save(
            new Product(
                $uuid,
                'Product 1',
                new Money(
                    'PLN',
                    100,
                    20
                ),
            )
        );
        $this->transactionManager->forceException = true;

        self::assertEquals(
            Result::unexpectedError(),
            $this->deleteProduct->execute(
                new DeleteProductCommand($uuid)
            )
        );
    }

    protected function setUp(): void
    {
        $this->productRepository = new InMemoryProductRepository();
        $productCategoryRepository = new InMemoryProductCategoryRepository();
        $this->transactionManager = new InMemoryTransactionManager();

        $this->deleteProduct = new DeleteProduct(
            $this->productRepository,
            $productCategoryRepository,
            $this->transactionManager
        );
    }
}
