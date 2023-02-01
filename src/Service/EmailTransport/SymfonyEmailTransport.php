<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service\EmailTransport;

use Dangle\Mailer\Model\OutgoingEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class SymfonyEmailTransport implements EmailTransportInterface
{
    public function send(OutgoingEmail $email)
    {
        // TODO: validate $email fields or validate OutgoingEmail entity?
        $transport = Transport::fromDsn($email->dsn);
        $mailer = new Mailer($transport);
        $mail = (new Email())
            ->from($email->from)
            ->to($email->to)
            ->subject($email->subject)
            ->text($email->text);

        foreach ($email->attachments as $attachment) {
            $mail->attachFromPath($attachment);
        }

        $mailer->send($mail);
    }
}
