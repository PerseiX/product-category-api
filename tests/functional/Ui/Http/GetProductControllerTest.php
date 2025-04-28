<?php

declare(strict_types=1);

namespace App\Tests\functional\Ui\Http;

use App\Products\Application\Query\CategoryCollectionView;
use App\Products\Application\Query\CategoryView;
use App\Products\Application\Query\GetProductViewQuery;
use App\Products\Application\Query\ProductView;
use App\Tests\kit\Query\InMemoryGetProductViewQuery;
use DateTime;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GetProductControllerTest extends WebTestCase
{
    private const URL = '/api/products/%s';

    private KernelBrowser $client;
    private GetProductViewQuery $query;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->query = new InMemoryGetProductViewQuery();
        $this->getContainer()->set(
            'App\Products\Application\Query\GetProductViewQuery',
            $this->query
        );
    }

    public function testInvalidUuid(): void
    {
        $this->client->request('GET', sprintf(self::URL, 'a-b-c'));

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Invalid UUID']),
            $response->getContent()
        );
    }

    public function testProductNotFound(): void
    {
        $productId = Uuid::uuid4();
        $this->client->request('GET', sprintf(self::URL, $productId->toString()));

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Product not found']),
            $response->getContent()
        );
    }

    public function testProductFound(): void
    {
        $product1Created = new DateTimeImmutable();
        $product1Updated = new DateTimeImmutable();
        $category1Created = new DateTimeImmutable();
        $category1Updated = new DateTimeImmutable();
        $category2Created = new DateTimeImmutable();
        $category2Updated = new DateTimeImmutable();

        $productId1 = Uuid::uuid4();
        $this->query->withData(
            new ProductView(
                $productId1,
                'Product 1',
                'PLN',
                '20.5',
                $product1Created,
                $product1Updated,
                new CategoryCollectionView(
                    [
                        new CategoryView(
                            1,
                            '123456789',
                            'Category name',
                            $category1Created,
                            $category1Updated,
                        ),
                        new CategoryView(
                            2,
                            '123456789',
                            'Category name',
                            $category2Created,
                            $category2Updated
                        )
                    ]
                )
            ),
        );

        $this->client->request('GET', sprintf(self::URL, $productId1->toString()));

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonStringEqualsJsonString(
            <<<Response
             {
              "productId": "{$productId1->toString()}",
              "name": "Product 1",
              "currency": "PLN",
              "price": "20.5",
              "createdAt": "{$product1Created->format(DateTime::ATOM)}",
              "updatedAt": "{$product1Updated->format(DateTime::ATOM)}",
              "categoryCollection": {
                "categoryCollection": [
                  {
                    "categoryId": 1,
                    "code": "123456789",
                    "name": "Category name",
                    "createdAt": "{$category1Created->format(DateTime::ATOM)}",
                    "updatedAt": "{$category1Updated->format(DateTime::ATOM)}"
                  },
                  {
                    "categoryId": 2,
                    "code": "123456789",
                    "name": "Category name",
                    "createdAt": "{$category2Created->format(DateTime::ATOM)}",
                    "updatedAt": "{$category2Updated->format(DateTime::ATOM)}"
                  }
                ]
              }
            }
            Response,
            $response->getContent()
        );
    }
}
