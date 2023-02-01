<?php

namespace Dangle\Mailer\Service\Console;

interface StdInInterface
{
    public function getInput(): string;
}
