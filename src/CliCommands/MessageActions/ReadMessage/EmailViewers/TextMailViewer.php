<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\MessageActions\ReadMessage\EmailViewers;

use Dangle\Mailer\Model\Email;
use Dangle\Mailer\Service\Filesystem\SaveToFileInterface;
use Dangle\Mailer\Service\Process\ProcessInterface;
use Dangle\Mailer\Service\Process\ProcessTtyInterface;

class TextMailViewer implements MailViewerInterface
{
    private ProcessTtyInterface $processTty;
    private ProcessInterface $process;
    private SaveToFileInterface $saveToFile;

    public function __construct(ProcessTtyInterface $processTty, ProcessInterface $process, SaveToFileInterface $saveToFile)
    {
        $this->processTty = $processTty;
        $this->process = $process;
        $this->saveToFile = $saveToFile;
    }

    public function view(Email $email, string $tmpFile)
    {
        $this->saveToFile->save($tmpFile, $this->_parseEmail($email));

        // use less to display message
        $command = "less +gg {$tmpFile}";
        $this->processTty->run($command);

        // delete temp file
        $this->process->run("rm {$tmpFile}");
    }

    private function _parseEmail(Email $email): string
    {
        return <<<EMAIL
From: {$email->from}
To: {$email->deliveredTo}
Subject: {$email->subject}
Date: {$email->date}
------------------------------------

{$email->text}
EMAIL;
    }
}
