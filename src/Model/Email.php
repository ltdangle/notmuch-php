<?php

declare(strict_types=1);

namespace Dangle\Mailer\Model;

class Email
{
    /**
     * Path to the message file on the filesystem.
     */
    public string $path = '';

    public string $from = '';
    public string $to = '';
    public string $deliveredTo = '';
    public string $subject = '';
    public string $text = '';
    public string $html = '';
    public string $date = '';
    public bool $isSeen = false;
    public bool $isImportant = false;
    public bool $isAnswered = false;
    /**
     * Is email selected in the interface?
     */
    public bool $isSelected = false;
}
