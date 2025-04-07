<?php

declare(strict_types=1);

namespace App\Tests\functional\Ui\Controller;

use App\Products\Application\Query\CategoryCollectionView;
use App\Products\Application\Query\CategoryView;
use App\Products\Application\Query\GetAllProductsQuery;
use App\Products\Application\Query\ProductCollectionView;
use App\Products\Application\Query\ProductView;
use App\Products\Infrastructure\Persistence\Query\InMemoryGetAllProductsQuery;
use DateTime;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GetProductsControllerTest extends WebTestCase
{
    private const URL = '/api/products';

    private GetAllProductsQuery $query;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->query = new InMemoryGetAllProductsQuery();
        $this->getContainer()->set(
            'App\Products\Application\Query\GetAllProductsQuery',
            $this->query
        );
    }

    public function testEmptyCollection(): void
    {
        $this->client->request('GET', self::URL);

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Products not found']),
            $response->getContent()
        );
    }

    public function testCollectionFound(): void
    {
        $product1Created = new DateTimeImmutable();
        $product2Created = new DateTimeImmutable();
        $product1Updated = new DateTimeImmutable();
        $product2Updated = new DateTimeImmutable();
        $category1Created = new DateTimeImmutable();
        $category1Updated = new DateTimeImmutable();
        $category2Created = new DateTimeImmutable();
        $category2Updated = new DateTimeImmutable();

        $productId1 = Uuid::uuid4();
        $productId2 = Uuid::uuid4();
        $this->query->withData(
            new ProductCollectionView([
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
                new ProductView(
                    $productId2,
                    'Product 2',
                    'EUR',
                    '33.5',
                    $product2Created,
                    $product2Updated,
                    new CategoryCollectionView(
                        [
                            new CategoryView(
                                1,
                                '123456789',
                                'Category name',
                                $category1Created,
                                $category1Updated,
                            ),
                        ]
                    )
                )
            ])
        );
        $this->client->request('GET', self::URL);

        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonStringEqualsJsonString(
            <<<Response
            {
              "productCollectionView": [
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
                        "createdAt": "{$category1Updated->format(DateTime::ATOM)}",
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
                },
                {
                  "productId": "{$productId2->toString()}",
                  "name": "Product 2",
                  "currency": "EUR",
                  "price": "33.5",
                  "createdAt": "{$product2Created->format(DateTime::ATOM)}",
                  "updatedAt": "{$product2Updated->format(DateTime::ATOM)}",
                  "categoryCollection": {
                    "categoryCollection": [
                      {
                        "categoryId": 1,
                        "code": "123456789",
                        "name": "Category name",
                        "createdAt": "{$category1Updated->format(DateTime::ATOM)}",
                        "updatedAt": "{$category1Updated->format(DateTime::ATOM)}"
                      }
                    ]
                  }
                }
              ]
            }
            Response,
            $response->getContent()
        );
    }
}