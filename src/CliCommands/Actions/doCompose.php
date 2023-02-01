<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\Actions;

use Dangle\Mailer\Model\EmailAccount;
use Dangle\Mailer\Model\OutgoingEmail;
use Dangle\Mailer\Service\Console\ConsoleAskInterface;
use Dangle\Mailer\Service\EmailTransport\EmailTransportInterface;
use Dangle\Mailer\Service\Filesystem\ReadFromFileInterface;
use Dangle\Mailer\Service\Filesystem\SaveToFileInterface;
use Dangle\Mailer\Service\Process\ProcessInterface;
use Dangle\Mailer\Service\Process\ProcessTtyInterface;

class doCompose
{
    private string $headerSeparator = "=============Header end=============\n";
    private EmailTransportInterface $emailTransport;
    private ConsoleAskInterface $consoleAsk;
    private ProcessTtyInterface $processTty;
    private ProcessInterface $process;
    private SaveToFileInterface $saveToFile;
    private ReadFromFileInterface $readFromFile;

    public function __construct(EmailTransportInterface $emailTransport, ConsoleAskInterface $consoleAsk, ProcessTtyInterface $processTty, ProcessInterface $process, SaveToFileInterface $saveToFile, ReadFromFileInterface $readFromFile)
    {
        $this->emailTransport = $emailTransport;
        $this->consoleAsk = $consoleAsk;
        $this->processTty = $processTty;
        $this->process = $process;
        $this->saveToFile = $saveToFile;
        $this->readFromFile = $readFromFile;
    }

    public function doComposeMessage(string $tmpFile, string $from, string $to, string $subject, EmailAccount $emailAccount, ?string $message, array $attachments = []): bool
    {
        // write email header to temp file
        $this->saveToFile->save($tmpFile, $this->header($from, $to, $subject, $message, $attachments));

        // compose email reply
        $this->processTty->run("nvim -c \"+ normal Go\" {$tmpFile} > `tty`");

        // parse email reply
        $tmpFileContentsArr = explode($this->getHeaderSeparator(), $this->readFromFile->read($tmpFile));
        $msgBody = trim($tmpFileContentsArr[1]);

        // send email
        $email = new OutgoingEmail();
        $email->to = $to;
        $email->from = $from;
        $email->text = $msgBody;
        $email->subject = $subject;
        $email->dsn = $emailAccount->dsn;
        $email->attachments = $attachments;

        // Ask user for confirmation
        if ('y' !== $this->consoleAsk->ask('Send message (y/n)? ')) {
            $this->process->run("rm {$tmpFile}");

            return false;
        }

        $this->emailTransport->send($email);

        $this->process->run("rm {$tmpFile}");

        return true;
    }

    private function header(string $from, string $to, string $subject, ?string $message, array $attachments): string
    {
        $h = "From: $from\n";
        $h .= "To: $to\n";
        $h .= "Subject: $subject\n";
        if ($attachments) {
            $h .= 'Attachments:';
            foreach ($attachments as $attachment) {
                $h .= ' '.$attachment;
            }
            $h .= "\n";
        }
        $h .= $this->getHeaderSeparator();
        $h .= "\n".$message."\n";

        return $h;
    }

    public function getHeaderSeparator(): string
    {
        return $this->headerSeparator;
    }

    public function setHeaderSeparator(string $headerSeparator): void
    {
        $this->headerSeparator = $headerSeparator;
    }
}
