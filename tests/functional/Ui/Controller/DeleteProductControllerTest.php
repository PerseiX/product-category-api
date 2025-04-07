<?php

declare(strict_types=1);

namespace App\Tests\functional\Ui\Controller;

use App\Products\Application\UseCase\Delete\DeleteProductInterface;
use App\Products\Application\UseCase\Delete\Result;
use App\Tests\kit\InMemoryDeleteProductUseCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DeleteProductControllerTest extends WebTestCase
{
    private const URL = '/api/products/%s';

    private DeleteProductInterface $deleteProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->deleteProduct = new InMemoryDeleteProductUseCase();
        $this->getContainer()->set(
            'App\Products\Application\UseCase\Delete\DeleteProductInterface',
            $this->deleteProduct
        );
    }

    public function testProductNotFound(): void
    {
        $this->deleteProduct->withResult(Result::productNotFound());

        $productId = 'b1c2d3e4-f5a6-7b8c-9d0e-f1a2b3c4d5e6';
        $this->client->request('DELETE', sprintf(self::URL, $productId));

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Product not found']),
            $response->getContent()
        );
    }

    public function testSuccessfullyDeleted(): void
    {
        $this->deleteProduct->withResult(Result::success());

        $productId = 'b1c2d3e4-f5a6-7b8c-9d0e-f1a2b3c4d5e6';
        $this->client->request('DELETE', sprintf(self::URL, $productId));
        $this->client->getResponse();

        $this->assertResponseStatusCodeSame(200);
    }
}