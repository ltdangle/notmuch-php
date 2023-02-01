<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\Actions;

use Dangle\Mailer\Settings;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ComposeMessage
{
    private Settings $settings;
    private doCompose $doCompose;
    private string $tmpFile;
    private QuestionHelper $helper;
    private InputInterface $input;
    private OutputInterface $output;

    public function __construct(Settings $settings, doCompose $doCompose, string $tmpFile, QuestionHelper $helper, InputInterface $input, OutputInterface $output)
    {
        $this->settings = $settings;
        $this->doCompose = $doCompose;
        $this->tmpFile = $tmpFile;
        $this->helper = $helper;
        $this->input = $input;
        $this->output = $output;
    }

    public function compose(string $accAlias, array $attachments)
    {
        $emailAccount = $this->settings->getAccountByAlias($accAlias);
        if (!$emailAccount) {
            throw new \InvalidArgumentException("Account $accAlias is not configured");
        }

        $to = trim($this->helper->ask($this->input, $this->output, new Question('Send email to: ')));
        $subject = trim($this->helper->ask($this->input, $this->output, new Question('Subject: ')));
        $from = $emailAccount->email;
        $this->doCompose->doComposeMessage($this->tmpFile, $from, $to, $subject, $emailAccount, null, $attachments);
    }
}
