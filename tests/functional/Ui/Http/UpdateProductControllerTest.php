<?php

declare(strict_types=1);

namespace App\Tests\functional\Ui\Http;

use App\Products\Application\UseCase\Create\AddProductInterface;
use App\Products\Application\UseCase\Create\Result as CreateResult;
use App\Products\Application\UseCase\Update\Result;
use App\Products\Application\UseCase\Update\UpdateProductInterface;
use App\Tests\kit\UseCase\InMemoryAddProductUseCase;
use App\Tests\kit\UseCase\InMemoryUpdateProductUseCase;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UpdateProductControllerTest extends WebTestCase
{
    private const string URL = '/api/products/%s';
    
    private KernelBrowser $client;
    private UpdateProductInterface $updateProduct;
    private AddProductInterface $addProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->updateProduct = new InMemoryUpdateProductUseCase();
        $this->addProduct = new InMemoryAddProductUseCase();

        $this->getContainer()->set(
            'App\Products\Application\UseCase\Update\UpdateProductInterface',
            $this->updateProduct
        );

        $this->getContainer()->set(
            'App\Products\Application\UseCase\Create\AddProductInterface',
            $this->addProduct
        );
    }

    public function testNameIsRequired(): void
    {
        $productId = 'b1c2d3e4-f5a6-7b8c-9d0e-f1a2b3c4d5e6';
        $this->client->request(
            'PUT',
            uri: sprintf(self::URL, $productId),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Name is required']),
            $response->getContent()
        );
    }

    public function testPriceIsRequired(): void
    {
        $productId = 'b1c2d3e4-f5a6-7b8c-9d0e-f1a2b3c4d5e6';
        $this->client->request(
            'PUT',
            uri: sprintf(self::URL, $productId),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'name' => 'Name product'
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Price is required']),
            $response->getContent()
        );
    }

    public function testCurrencyIsRequired(): void
    {
        $productId = 'b1c2d3e4-f5a6-7b8c-9d0e-f1a2b3c4d5e6';
        $this->client->request(
            'PUT',
            uri: sprintf(self::URL, $productId),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => '10.5',
                'name' => 'Product 1'
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Currency is required']),
            $response->getContent()
        );
    }

    public function testCategoriesIsRequired(): void
    {
        $productId = 'b1c2d3e4-f5a6-7b8c-9d0e-f1a2b3c4d5e6';
        $this->client->request(
            'PUT',
            uri: sprintf(self::URL, $productId),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => '10.5',
                'name' => 'Product 1',
                'currency' => 'PLN'
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Categories are required']),
            $response->getContent()
        );
    }

    public function testEmptyCategory(): void
    {
        $this->updateProduct->withResult(Result::categoryIsRequired());

        $productId = 'b1c2d3e4-f5a6-7b8c-9d0e-f1a2b3c4d5e6';
        $this->client->request(
            'PUT',
            uri: sprintf(self::URL, $productId),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => '10.5',
                'name' => 'Product 1',
                'currency' => 'PLN',
                'categories' => []
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Category is required']),
            $response->getContent()
        );
    }

    public function testCategoryNotFound(): void
    {
        $this->updateProduct->withResult(Result::categoryNotFound());

        $productId = 'b1c2d3e4-f5a6-7b8c-9d0e-f1a2b3c4d5e6';
        $this->client->request(
            'PUT',
            uri: sprintf(self::URL, $productId),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => '10.5',
                'name' => 'Product 1',
                'currency' => 'PLN',
                'categories' => [1]
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Category not found']),
            $response->getContent()
        );
    }

    public function testProductNotFoundAndCreated(): void
    {
        $productId = 'b1c2d3e4-f5a6-7b8c-9d0e-f1a2b3c4d5e6';

        $this->updateProduct->withResult(Result::productNotFound());
        $this->addProduct->withResult(CreateResult::success(Uuid::fromString($productId)));

        $this->client->request(
            'PUT',
            uri: sprintf(self::URL, $productId),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => '10.5',
                'name' => 'Product 1',
                'currency' => 'PLN',
                'categories' => [1]
            ])
        );

        $this->client->getResponse();

        $this->assertResponseStatusCodeSame(200);
    }

    public function testSuccessfullyUpdated(): void
    {
        $this->updateProduct->withResult(Result::success());

        $productId = 'b1c2d3e4-f5a6-7b8c-9d0e-f1a2b3c4d5e6';
        $this->client->request(
            'PUT',
            uri: sprintf(self::URL, $productId),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => '10.5',
                'name' => 'Product 1',
                'currency' => 'PLN',
                'categories' => [1]
            ])
        );

        $this->client->getResponse();

        $this->assertResponseStatusCodeSame(200);
    }

    public function testPriceIsInvalid(): void
    {
        $this->updateProduct->withResult(Result::invalidPrice());

        $productId = 'b1c2d3e4-f5a6-7b8c-9d0e-f1a2b3c4d5e6';
        $this->client->request(
            'PUT',
            uri: sprintf(self::URL, $productId),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => 'sadfsadf',
                'name' => 'Product 1',
                'currency' => 'PLN',
                'categories' => [1]
            ])
        );

        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'The price is invalid']),
            $response->getContent()
        );
    }
}
