<?php

declare(strict_types=1);

namespace Dangle\Mailer\Events;

use Symfony\Contracts\EventDispatcher\Event;

class FilesystemChangedEvent extends Event
{
    public const NAME = 'filesystem.changed';

    public function __construct()
    {
    }
}
