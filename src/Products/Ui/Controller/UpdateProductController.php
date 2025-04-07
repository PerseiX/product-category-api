<?php

declare(strict_types=1);

namespace App\Products\Ui\Controller;

use App\Products\Application\UseCase\Create\AddProductCommand;
use App\Products\Application\UseCase\Create\AddProductInterface;
use App\Products\Application\UseCase\Update\UpdateProductCommand;
use App\Products\Application\UseCase\Update\UpdateProductInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

final class UpdateProductController
{
    public function __construct(
        private readonly UpdateProductInterface $updateProduct,
        private readonly AddProductInterface    $addProduct
    )
    {
    }

    #[Route('/api/products/{id}', methods: ['PUT'])]
    public function __invoke(Request $request, string $id): Response
    {
        try {
            $uuid = Uuid::fromString($id);
        } catch (Throwable) {
            return new JsonResponse(['message' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);

        if (!array_key_exists('name', $data)) {
            return new JsonResponse(['message' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!array_key_exists('price', $data)) {
            return new JsonResponse(['message' => 'Price is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!array_key_exists('currency', $data)) {
            return new JsonResponse(['message' => 'Currency is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!array_key_exists('categories', $data)) {
            return new JsonResponse(['message' => 'Categories are required'], Response::HTTP_BAD_REQUEST);
        }

        $command = new UpdateProductCommand(
            id: $uuid,
            name: $data['name'],
            categories: $data['categories'],
            price: $data['price'],
            currency: $data['currency']
        );

        $result = $this->updateProduct->execute($command);

        if ($result->isSuccess()) {
            return new JsonResponse(Response::HTTP_NO_CONTENT);
        }

        if ('product_not_found' === $result->getReason()) {
            $addProduct = new AddProductCommand(
                name: $data['name'],
                price: $data['price'],
                currency: $data['currency'],
                categories: $data['categories'],
                id: $uuid,
            );

            $result = $this->addProduct->execute($addProduct);

            if ($result->isSuccess()) {
                return new JsonResponse(Response::HTTP_NO_CONTENT);
            }
        }

        return match ($result->getReason()) {
            'category_not_found' => new JsonResponse(['message' => 'Category not found'], Response::HTTP_NOT_FOUND),
            'invalid_price' => new JsonResponse(['message' => 'The price is invalid'], Response::HTTP_BAD_REQUEST),
            'category_is_required' => new JsonResponse(['message' => 'Category is required'], Response::HTTP_NOT_FOUND),
            'product_not_found' => new JsonResponse(['message' => 'Product not found'], Response::HTTP_NOT_FOUND),
            'whole_negative' => new JsonResponse(['message' => 'The price must be positive'], Response::HTTP_BAD_REQUEST),
            'rest_out_of_the_range' => new JsonResponse(['message' => 'The rest must be between 0 and 99'], Response::HTTP_BAD_REQUEST),
            default => new JsonResponse(['message' => 'Failed to add product'], Response::HTTP_BAD_REQUEST)
        };
    }
}