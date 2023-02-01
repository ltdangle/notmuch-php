<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\MessageActions;

use Dangle\Mailer\CliCommands\View\EmailPrinter;
use Dangle\Mailer\Service\MessageService\MessageServiceInterface;
use Symfony\Component\Console\Question\Question;

class DeleteMessageAction implements MessageActionInterface
{
    private string $command;
    private MessageServiceInterface $service;
    private EmailPrinter $emailPrinter;

    public function __construct(string $command, MessageServiceInterface $service, EmailPrinter $emailPrinter)
    {
        $this->command = $command;
        $this->service = $service;
        $this->emailPrinter = $emailPrinter;
    }

    public function execute(MessageActionParams $params): void
    {
        // preview email set for deletion
        $this->emailPrinter->show($params->output, $params->emails);

        // TODO: move question and console helpers to constructor DI
        $answer = $params->questionHelper->ask(
            $params->input,
            $params->output,
            new Question("Do you want to delete message(s)' (y/n)? ")
        );

        if ('y' === $answer) {
            $this->service->delete($params->emails);
        }
    }

    public function command(): string
    {
        return $this->command;
    }
}
