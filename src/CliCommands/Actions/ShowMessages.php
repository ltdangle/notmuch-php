<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\Actions;

use Dangle\Mailer\CliCommands\View\EmailPrinter;
use Dangle\Mailer\Service\MessageService\MessageServiceInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowMessages
{
    private MessageServiceInterface $service;
    private EmailPrinter $emailPrinter;
    private OutputInterface $output;

    public function __construct(MessageServiceInterface $service, EmailPrinter $emailPrinter, OutputInterface $output)
    {
        $this->service = $service;
        $this->emailPrinter = $emailPrinter;
        $this->output = $output;
    }

    public function show(string $accAlias): void
    {
        $emails = $this->service->emails($accAlias)->getAll();
        $this->emailPrinter->show($this->output, $emails);
    }
}
