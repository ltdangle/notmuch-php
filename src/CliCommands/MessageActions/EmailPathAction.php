<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\MessageActions;

use Dangle\Mailer\Service\Console\Console;
use Symfony\Component\Console\Command\Command;

class EmailPathAction implements MessageActionInterface
{
    private string $command;
    private Console $console;

    public function __construct(string $command, Console $console)
    {
        $this->command = $command;
        $this->console = $console;
    }

    public function command(): string
    {
        return $this->command;
    }

    public function execute(MessageActionParams $params): void
    {
        foreach ($params->emails as $email) {
            $this->console->writeLn($email->path);
        }

        exit(Command::SUCCESS);
    }
}
