<?php

declare(strict_types=1);

namespace Dangle\Mailer\Events;

use Dangle\Mailer\Service\Console\ConsoleWriteInterface;

class ConsoleNotificationEventListener
{
    private ConsoleWriteInterface $consoleWrite;

    public function __construct(ConsoleWriteInterface $consoleWrite)
    {
        $this->consoleWrite = $consoleWrite;
    }

    public function __invoke(ConsoleNotificationEvent $event)
    {
        $this->consoleWrite->writeLn($event->getNotification());
    }
}
