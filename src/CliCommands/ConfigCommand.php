<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands;

use Dangle\Mailer\Model\EmailAccount;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'config',
    description: 'Configures email accounts',
)]
class ConfigCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $acc = new EmailAccount();
        $a = [
            $this->shortName(...),
            $this->regularOrVirtualAccount(...),
            $this->email(...),
            $this->shellCommand(...),
            $this->dsn(...),
            $this->trashDir(...),
        ];
        foreach ($a as $command) {
            $this->displayAccount($acc);
            $command($acc);
        }
        // Do you know what I'm saying?
        // 0. Is it a virtual or regular email account?
        // 1. Choose account shortname (alias).
        // 2. Create email.
        // 3. Create shell command.
        // 4. Create dsn.
        // 5. Create deleted folder.

        return Command::SUCCESS;
    }

    protected function regularOrVirtualAccount(EmailAccount $account): void
    {
        $account->accountType = $this->io->choice('Is this regular or virtual (i.e. notmuch search) account?', ['regular', 'virtual']);
    }

    protected function shortName(EmailAccount $account)
    {
        $account->shortName = $this->io->askQuestion(new Question('Please pick short name for this account'));
    }

    protected function email(EmailAccount $account)
    {
    }

    protected function shellCommand()
    {
    }

    protected function dsn()
    {
    }

    protected function trashDir()
    {
    }

    protected function displayAccount(EmailAccount $account)
    {
        $this->clearScreen();
        $this->io->text('Settings for account '.$account->shortName);
        $this->io->horizontalTable(
            ['Account type', 'Short name', 'Email address', 'Shell command', 'Trash folder'],
            [[$account->accountType, $account->shortName, $account->email, $account->inboxShellCommand, $account->trashFolder]]
        );
    }

    protected function clearScreen()
    {
        $this->io->write(sprintf("\033\143"));
    }
}
