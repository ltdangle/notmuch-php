<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service\Console;

class StdIn implements StdInInterface
{
    public function getInput(): string
    {
        return trim(file_get_contents('php://stdin'));
    }
}
