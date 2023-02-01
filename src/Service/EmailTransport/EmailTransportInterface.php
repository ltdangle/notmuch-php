<?php

namespace Dangle\Mailer\Service\EmailTransport;

use Dangle\Mailer\Model\OutgoingEmail;

interface EmailTransportInterface
{
    public function send(OutgoingEmail $email);
}
