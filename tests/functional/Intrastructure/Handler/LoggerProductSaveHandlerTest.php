<?php

declare(strict_types=1);

namespace App\Tests\functional\Intrastructure\Handler;

use App\Products\Application\Event\ProductSaved;
use App\Products\Infrastructure\Handler\LoggerProductSaveHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class LoggerProductSaveHandlerTest extends KernelTestCase
{
    public function testLogging(): void
    {
        $container = static::getContainer();

        $handler = $container->get(LoggerProductSaveHandler::class);
        $productId = Uuid::uuid4();

        $handler(new ProductSaved($productId, 'Awesome product'));
        /** @var TestLogger $logger */
        $logger = $container->get(LoggerInterface::class);

        $this->assertTrue(
            $logger->hasInfo("[PRODUCT_SAVE] The product ({$productId->toString()}) been saved.")
        );
    }
}
