<?php

declare(strict_types=1);

namespace App\Tests\functional\Ui\Controller;

use App\Products\Application\UseCase\Create\AddProductInterface;
use App\Products\Application\UseCase\Create\Result;
use App\Tests\kit\InMemoryAddProductUseCase;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AddProductControllerTest extends WebTestCase
{
    private const URL = '/api/products';

    private AddProductInterface $addProductUseCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->addProductUseCase = new InMemoryAddProductUseCase();
        $this->getContainer()->set(
            'App\Products\Application\UseCase\Create\AddProductInterface',
            $this->addProductUseCase
        );
    }

    public function testNameIsRequired(): void
    {
        $this->client->request('POST',
            uri: self::URL,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
            ]));

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Name is required']),
            $response->getContent()
        );
    }

    public function testPriceIsRequired(): void
    {
        $this->client->request('POST',
            uri: self::URL,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'name' => 'Name product'
            ]));

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Price is required']),
            $response->getContent()
        );
    }

    public function testCurrencyIsRequired(): void
    {
        $this->client->request('POST',
            uri: self::URL,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => '10.5',
                'name' => 'Product 1'
            ]));

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Currency is required']),
            $response->getContent()
        );
    }

    public function testCategoriesIsRequired(): void
    {
        $this->client->request('POST',
            uri: self::URL,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => '10.5',
                'name' => 'Product 1',
                'currency' => 'PLN'
            ]));

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Categories are required']),
            $response->getContent()
        );
    }

    public function testSuccess(): void
    {
        $this->addProductUseCase->withResult(Result::success(Uuid::uuid4()));
        $this->client->request('POST',
            uri: self::URL,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => '10.5',
                'name' => 'Product 1',
                'currency' => 'PLN',
                'categories' => [1]
            ]));

        $this->assertResponseStatusCodeSame(200);
    }

    public function testCategoryNotFound(): void
    {
        $this->addProductUseCase->withResult(Result::categoryNotFound());
        $this->client->request('POST',
            uri: self::URL,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => '10.5',
                'name' => 'Product 1',
                'currency' => 'PLN',
                'categories' => [2]
            ]));

        $this->assertResponseStatusCodeSame(404);
        $response = $this->client->getResponse();

        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Category not found']),
            $response->getContent()
        );
    }

    public function testCategoryIsRequired(): void
    {
        $this->addProductUseCase->withResult(Result::categoryIsRequired());
        $this->client->request('POST',
            uri: self::URL,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => '10.5',
                'name' => 'Product 1',
                'currency' => 'PLN',
                'categories' => [2]
            ]));

        $this->assertResponseStatusCodeSame(400);
        $response = $this->client->getResponse();

        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Category is required']),
            $response->getContent()
        );
    }


    public function testInvalidPrice(): void
    {
        $this->addProductUseCase->withResult(Result::invalidPrice());
        $this->client->request('POST',
            uri: self::URL,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'price' => 'sadf.5',
                'name' => 'Product 1',
                'currency' => 'PLN',
                'categories' => [2]
            ]));

        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(400);

        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'The price is invalid']),
            $response->getContent()
        );
    }
}