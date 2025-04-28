<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\Handler;

use App\Products\Application\Event\ProductSaved;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;

#[AsMessageHandler]
final class MailProductSavedHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string          $emailFrom,
        private readonly string          $emailTo,
    ) {
    }

    public function __invoke(ProductSaved $productSaved): void
    {
        $email = new TemplatedEmail()
            ->from($this->emailFrom)
            ->to(new Address($this->emailTo))
            ->subject('Product has been saved!!')
            ->htmlTemplate('emails/product-saved.html.twig')
            ->locale('en')
            ->context([
                'productId' => $productSaved->productId,
                'productName' => $productSaved->productName,
            ]);

        $this->mailer->send($email);
    }
}
