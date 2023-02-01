<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service\MailSync;

use Dangle\Mailer\Service\NotMuch\NotMuchInterface;
use Symfony\Component\Process\Process;

class MailSync implements MailSyncInterface
{
    private NotMuchInterface $notMuch;

    public function __construct(NotMuchInterface $notMuch)
    {
        $this->notMuch = $notMuch;
    }

    public function sync()
    {
        $command = 'mbsync -a';
        $process = Process::fromShellCommandline($command);
        $process->start();

        while ($process->isRunning()) {
            echo '.';
            sleep(1);
        }

        $this->notMuch->updateDb();
    }
}
