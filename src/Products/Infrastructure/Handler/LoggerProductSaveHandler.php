<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Handler;

use App\Products\Application\Event\ProductSaved;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class LoggerProductSaveHandler
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(ProductSaved $productSaved): void
    {
        $this->logger->info("[PRODUCT_SAVE] The product ({$productSaved->productId}) been saved.");
    }
}