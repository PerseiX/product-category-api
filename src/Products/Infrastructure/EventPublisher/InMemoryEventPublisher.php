<?php

declare(strict_types=1);

namespace App\Products\Infrastructure\EventPublisher;

use App\Products\Application\Event\Event;
use App\Products\Application\Event\EventPublisher;

final class InMemoryEventPublisher implements EventPublisher
{
    public array $events = [];

    public function publish(Event $event): void
    {
        $this->events[] = $event;
    }
}
