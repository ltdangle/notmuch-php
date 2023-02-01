<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\MessageActions;

use Dangle\Mailer\Service\MessageService\MessageServiceInterface;

class ToggleFlagAction implements MessageActionInterface
{
    private string $command;
    private MessageServiceInterface $service;

    public function __construct(string $command, MessageServiceInterface $service)
    {
        $this->command = $command;
        $this->service = $service;
    }

    public function execute(MessageActionParams $params): void
    {
        foreach ($params->emails as $email) {
            $this->service->toggleFlag($email);
        }
    }

    public function command(): string
    {
        return $this->command;
    }
}
