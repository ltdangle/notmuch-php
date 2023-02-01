<?php

namespace Dangle\Mailer\Service\Process;

interface ProcessInterface
{
    public function run(string $command): void;

    public function getOutput(): string;

    public function getExitCode(): ?int;
}
