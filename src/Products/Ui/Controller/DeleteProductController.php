<?php

declare(strict_types=1);

namespace App\Products\Ui\Controller;

use App\Products\Application\UseCase\Delete\DeleteProduct;
use App\Products\Application\UseCase\Delete\DeleteProductCommand;
use App\Products\Application\UseCase\Delete\DeleteProductInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

final class DeleteProductController
{
    public function __construct(
        private readonly DeleteProductInterface $deleteProduct
    )
    {
    }

    #[Route('/api/products/{id}', methods: ['DELETE'])]
    public function __invoke(string $id): Response
    {
        $uuid = Uuid::fromString($id);
        $command = new DeleteProductCommand($uuid);

        $result = $this->deleteProduct->execute($command);

        if ($result->isSuccess()) {
            return new JsonResponse(Response::HTTP_NO_CONTENT);
        }

        return match ($result->getReason()) {
            'product_not_found' => new JsonResponse(['message' => 'Product not found'], Response::HTTP_NOT_FOUND),
            default => new JsonResponse(['message' => 'Failed to delete product'], Response::HTTP_BAD_REQUEST)
        };
    }
}