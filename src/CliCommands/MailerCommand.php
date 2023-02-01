<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands;

use Dangle\Mailer\CliCommands\Actions\ComposeMessage;
use Dangle\Mailer\CliCommands\Actions\ShowMessages;
use Dangle\Mailer\CliCommands\MessageActions\MessageActionParams;
use Dangle\Mailer\CliCommands\MessageActions\MessageActionsCollection;
use Dangle\Mailer\Service\MessageService\MessageService;
use Dangle\Mailer\Settings;
use Dangle\Mailer\Util\NumRange;
use Dangle\Mailer\Util\RegexParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'check',
    description: 'Checks mail',
)]
class MailerCommand extends Command
{
    private Settings $settings;
    private ShowMessages $showMessages;
    private ComposeMessage $composeMessage;
    private MessageActionsCollection $messageActionsCollection;
    private MessageService $service;

    // configured during runtime
    private OutputInterface $output;
    private InputInterface $input;
    private QuestionHelper $questionHelper;
    private RegexParser $regexParser;

    public function __construct(Settings $settings, ShowMessages $showMessages, ComposeMessage $composeMessage, MessageActionsCollection $messageActionsCollection, MessageService $service, RegexParser $regexParser)
    {
        $this->settings = $settings;
        $this->showMessages = $showMessages;
        $this->composeMessage = $composeMessage;
        $this->messageActionsCollection = $messageActionsCollection;
        $this->service = $service;
        $this->regexParser = $regexParser;
        parent::__construct();
    }

    protected function configure(): void
    {
        // set a list of configured account aliases
        $accounts = [];
        foreach ($this->settings->getEmailAccounts() as $emailAccount) {
            $accounts[] = $emailAccount->shortName;
        }

        $this
            ->addArgument('account', InputArgument::REQUIRED, 'Email account: ('.implode(', ', $accounts).')')
            ->addArgument(
                'arguments',
                InputArgument::IS_ARRAY,
                'Arguments'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->input = $input;
        $this->questionHelper = $this->getHelper('question');

        $accAlias = $input->getArgument('account');
        $arguments = $input->getArgument('arguments');

        if (!$arguments) {
            $this->displayAllEmails($accAlias);

            return Command::SUCCESS;
        }

        $emailNumOrCompose = $arguments[0];

        // check if first letter is a number
        if (is_numeric($emailNumOrCompose[0])) {
            $emailAction = $arguments[1] ?? null;
            $arguments = array_slice($arguments, 2);
            $this->actionOnSelectedEmails($accAlias, $emailNumOrCompose, $emailAction, $arguments);

            return Command::SUCCESS;
        } elseif ('c' === $emailNumOrCompose) {
            array_shift($arguments);
            $this->composeEmail($accAlias, $arguments);

            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }

    private function clearScreen()
    {
        $this->output->write(sprintf("\033\143"));
    }

    private function displayAllEmails(mixed $accAlias): void
    {
        $this->clearScreen();
        $this->output->writeln("Account: $accAlias");
        $this->showMessages->show($accAlias);
    }

    private function actionOnSelectedEmails(mixed $accAlias, mixed $messages, mixed $messageAction, array $arguments): void
    {
        $numRange = $this->regexParser->numRange($messages);

        $this->emailAction($accAlias, $numRange, $messageAction, $arguments);

        $this->clearScreen();

        $this->showMessages->show($accAlias);
    }

    private function composeEmail(string $accAlias, array $attachments): void
    {
        $this->composeMessage->compose($accAlias, $attachments);
    }

    private function emailAction(mixed $accAlias, NumRange $messageRange, mixed $messageAction, array $arguments): void
    {
        $messageAction = $messageAction ?? 'r';

        $emails = $this->service->emails($accAlias)->get($messageRange);
        $params = new MessageActionParams();
        $params->messageRange = $messageRange;
        $params->accAlias = $accAlias;
        $params->emails = $emails;
        $params->input = $this->input;
        $params->output = $this->output;
        $params->questionHelper = $this->questionHelper;
        $params->arguments = $arguments;

        $this->messageActionsCollection->executeAction($messageAction, $params);
    }
}
