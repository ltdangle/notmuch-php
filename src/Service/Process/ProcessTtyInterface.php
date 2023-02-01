<?php

namespace Dangle\Mailer\Service\Process;

interface ProcessTtyInterface
{
    public function run(string $command): void;
}
