<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service\Process;

use Symfony\Component\Process\Process;

class ProcessTty implements ProcessTtyInterface
{
    public function run(string $command): void
    {
        $process = Process::fromShellCommandline($command);
        $process->setTty(true);
        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->run();
    }
}
