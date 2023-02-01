<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands;

use Dangle\Mailer\Service\MailSync\MailSyncInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'sync',
    description: 'Syncs mail',
)]
class SyncCommand extends Command
{
    private MailSyncInterface $mailSync;

    public function __construct(MailSyncInterface $mailSync)
    {
        $this->mailSync = $mailSync;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write('Syncing email...');
        $this->mailSync->sync();
        $output->writeln('');

        return Command::SUCCESS;
    }
}
