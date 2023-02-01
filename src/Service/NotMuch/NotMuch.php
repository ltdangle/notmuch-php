<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service\NotMuch;

use Symfony\Component\Process\Process;

class NotMuch implements NotMuchInterface
{
    public function updateDb(): void
    {
        $command = 'notmuch new';
        $process = Process::fromShellCommandline($command);
        $process->mustRun();
        $result_code = $process->getExitCode();

        if (0 !== $result_code) {
            throw new \RuntimeException("Command '$command' returned non-zero ($result_code) code");
        }
    }
}
