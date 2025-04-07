<?php

declare(strict_types=1);

namespace App\Products\Ui\Controller;

use App\Products\Application\Query\GetProductViewQuery;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

final class GetProductController
{
    public function __construct(
        private readonly GetProductViewQuery $query,
        private readonly SerializerInterface $serializer
    )
    {

    }

    #[Route('/api/products/{id}', methods: ['GET'])]
    public function __invoke(string $id): Response
    {
        try {
            $uuid = Uuid::fromString($id);
        } catch (Throwable $e) {
            return new JsonResponse(['message' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $productView = $this->query->execute($uuid);

        if (null === $productView) {
            return new JsonResponse(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = $this->serializer->serialize($productView, 'json');

        return JsonResponse::fromJsonString($jsonContent);
    }
}