<?php

declare(strict_types=1);

namespace App\Products\Ui\Controller;

use App\Products\Application\Query\GetAllProductsQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class GetProductsController
{
    public function __construct(
        private readonly GetAllProductsQuery $query,
        private readonly SerializerInterface $serializer
    )
    {

    }

    #[Route('/api/products', methods: ['GET'])]
    public function __invoke(): Response
    {
        $productViewCollection = $this->query->execute();

        if ($productViewCollection->productCollectionView === []) {
            return new JsonResponse(['message' => 'Products not found'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = $this->serializer->serialize($productViewCollection, 'json');

        return JsonResponse::fromJsonString($jsonContent);
    }
}