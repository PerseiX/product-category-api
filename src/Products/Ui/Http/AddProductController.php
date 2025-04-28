<?php

declare(strict_types=1);

namespace App\Products\Ui\Http;

use App\Products\Application\UseCase\Create\AddProductCommand;
use App\Products\Application\UseCase\Create\AddProductInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AddProductController
{
    public function __construct(
        private readonly AddProductInterface $addProduct
    ) {
    }

    #[Route('/api/products', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $requestData = json_decode($request->getContent(), true);

        if (!array_key_exists('name', $requestData)) {
            return new JsonResponse(['message' => 'Name is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!array_key_exists('price', $requestData)) {
            return new JsonResponse(['message' => 'Price is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!array_key_exists('currency', $requestData)) {
            return new JsonResponse(['message' => 'Currency is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!array_key_exists('categories', $requestData)) {
            return new JsonResponse(['message' => 'Categories are required'], Response::HTTP_BAD_REQUEST);
        }

        $command = new AddProductCommand(
            name: $requestData['name'],
            price: $requestData['price'],
            currency: $requestData['currency'],
            categories: $requestData['categories'],
            id: null
        );

        $result = $this->addProduct->execute($command);

        if ($result->isSuccess()) {
            return new JsonResponse(Response::HTTP_CREATED);
        }

        return match ($result->getReason()) {
            'category_not_found' =>
            new JsonResponse(['message' => 'Category not found'], Response::HTTP_NOT_FOUND),
            'invalid_price' =>
            new JsonResponse(['message' => 'The price is invalid'], Response::HTTP_BAD_REQUEST),
            'category_is_required' =>
            new JsonResponse(['message' => 'Category is required'], Response::HTTP_BAD_REQUEST),
            'whole_negative' =>
            new JsonResponse(['message' => 'The price must be positive'], Response::HTTP_BAD_REQUEST),
            'rest_out_of_the_range' =>
            new JsonResponse(['message' => 'The rest must be between 0 and 99'], Response::HTTP_BAD_REQUEST),
            default =>
            new JsonResponse(['message' => 'Failed to add product'], Response::HTTP_INTERNAL_SERVER_ERROR)
        };
    }
}
