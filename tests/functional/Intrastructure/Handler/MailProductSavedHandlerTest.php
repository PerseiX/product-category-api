<?php

declare(strict_types=1);

namespace App\Tests\functional\Intrastructure\Handler;

use App\Products\Application\Event\ProductSaved;
use App\Products\Infrastructure\Handler\MailProductSavedHandler;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class MailProductSavedHandlerTest extends KernelTestCase
{
    public function testMailSend(): void
    {
        $container = static::getContainer();

        $handler = $container->get(MailProductSavedHandler::class);
        $productId = Uuid::uuid4();

        $handler(new ProductSaved($productId, 'Awesome product'));
        $email = $this->getMailerMessage();

        $this->assertEmailHtmlBodyContains($email, 'The product has been saved!');
        $this->assertEmailHtmlBodyContains($email, "The product Awesome product ({$productId->toString()}) has been saved.");
    }
}