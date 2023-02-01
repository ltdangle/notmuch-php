<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\MessageActions\ReadMessage;

use Dangle\Mailer\CliCommands\MessageActions\MessageActionInterface;
use Dangle\Mailer\CliCommands\MessageActions\MessageActionParams;

class ReadMessageAsHtmlAction implements MessageActionInterface
{
    private string $command;
    private ReadMessageAction $readMessage;

    public function __construct(string $command, ReadMessageAction $readMessage)
    {
        $this->command = $command;
        $this->readMessage = $readMessage;
    }

    public function command(): string
    {
        return $this->command;
    }

    public function execute(MessageActionParams $params): void
    {
        $params->showHtml = true;
        $this->readMessage->execute($params);
    }
}
