<?php

declare(strict_types=1);

namespace Dangle\Mailer\Events;

use Symfony\Contracts\EventDispatcher\Event;

class ConsoleNotificationEvent extends Event
{
    public const NAME = 'console.notification';
    private string $notification;

    public function __construct(string $notification)
    {
        $this->notification = $notification;
    }

    public function getNotification(): string
    {
        return $this->notification;
    }
}
