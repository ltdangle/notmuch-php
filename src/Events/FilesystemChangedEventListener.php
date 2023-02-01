<?php

declare(strict_types=1);

namespace Dangle\Mailer\Events;

/**
 * Listens for changes to the maildir filesystem.
 */
class FilesystemChangedEventListener
{
    public function __invoke(FilesystemChangedEvent $event): void
    {
        // do nothing... for now
    }
}
