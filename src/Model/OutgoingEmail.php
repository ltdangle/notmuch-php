<?php

declare(strict_types=1);

namespace Dangle\Mailer\Model;

class OutgoingEmail
{
    public string $from = '';
    public string $to = '';
    public string $subject = '';
    public string $text = '';
    public string $dsn = '';
    public array $attachments = [];
}
