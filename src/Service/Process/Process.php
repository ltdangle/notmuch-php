<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service\Process;

class Process implements ProcessInterface
{
    private \Symfony\Component\Process\Process $process;

    public function run(string $command): void
    {
        $this->process = \Symfony\Component\Process\Process::fromShellCommandline($command);
        $this->process->run();
    }

    public function getOutput(): string
    {
        return $this->process->getOutput();
    }

    public function getExitCode(): ?int
    {
        return $this->process->getExitCode();
    }
}
