<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\MessageActions\ReadMessage\EmailViewers;

use Dangle\Mailer\Model\Email;
use Dangle\Mailer\Service\Filesystem\SaveToFileInterface;
use Symfony\Component\Process\Process;

class HtmlMailViewer implements MailViewerInterface
{
    private SaveToFileInterface $saveToFile;

    public function __construct(SaveToFileInterface $saveToFile)
    {
        $this->saveToFile = $saveToFile;
    }

    public function view(Email $email, string $tmpFile)
    {
        $tmpFile .= '.html';

        $this->saveToFile->save($tmpFile, $email->html);

        $process = Process::fromShellCommandline("open {$tmpFile}");
        $process->start();

        // wait until process finishes
        while ($process->isRunning()) {
            sleep(1);
        }

        // delete temp file
        $process = Process::fromShellCommandline("rm {$tmpFile}");
        $process->mustRun();
    }
}
