<?php

namespace Dangle\Mailer\CliCommands\MessageActions\ReadMessage\EmailViewers;

use Dangle\Mailer\Model\Email;

interface MailViewerInterface
{
    public function view(Email $email, string $tmpFile);
}
