<?php

declare(strict_types=1);


namespace App\Products\Application\Event;

interface EventPublisher
{
    public function publish(Event $event): void;
}