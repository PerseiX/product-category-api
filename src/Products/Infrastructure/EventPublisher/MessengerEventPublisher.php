<?php

declare(strict_types=1);


namespace App\Products\Infrastructure\EventPublisher;

use App\Products\Application\Event\Event;
use App\Products\Application\Event\EventPublisher;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerEventPublisher implements EventPublisher
{
    public function __construct(
        private readonly MessageBusInterface $bus
    ) {
    }

    public function publish(Event $event): void
    {
        try {
            $this->bus->dispatch($event);
        } catch (ExceptionInterface $e) {
            throw new \LogicException('Event can not be published');
        }
    }
}
