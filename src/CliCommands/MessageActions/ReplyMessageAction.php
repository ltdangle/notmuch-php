<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\MessageActions;

use Dangle\Mailer\CliCommands\Actions\doCompose;
use Dangle\Mailer\Model\Email;
use Dangle\Mailer\Service\MessageService\MessageServiceInterface;
use Dangle\Mailer\Settings;

class ReplyMessageAction implements MessageActionInterface
{
    private string $command;
    private string $tmpDir;
    private doCompose $doCompose;
    private MessageServiceInterface $service;
    private Settings $settings;

    public function __construct(string $command, string $tmpDir, doCompose $doCompose, MessageServiceInterface $service, Settings $settings)
    {
        $this->command = $command;
        $this->tmpDir = $tmpDir;
        $this->doCompose = $doCompose;
        $this->service = $service;
        $this->settings = $settings;
    }

    private function quoteReply(Email $email)
    {
        $q = "\nOn {$email->date} {$email->from} wrote:\n";
        $msgArr = explode("\n", $email->text);
        foreach ($msgArr as $line) {
            $q .= "> $line\n";
        }

        return $q;
    }

    public function execute(MessageActionParams $params): void
    {
        if (!$params->messageRange->isSingleDigit()) {
            throw new \InvalidArgumentException('You can read only one message at a time');
        }

        $emails = $this->service->emails($params->accAlias)->get($params->messageRange);
        /** @var Email $email */
        $email = array_pop($emails);

        $emailAccount = $this->settings->getAccountByEmail($email->deliveredTo);
        if (!$emailAccount) {
            throw new \InvalidArgumentException("Could not find account settings for {$email->to}");
        }

        // create temp filename
        $fileNameArr = explode(DIRECTORY_SEPARATOR, $email->path);
        $fileName = 'reply-'.$fileNameArr[count($fileNameArr) - 1];
        $tmpFile = $this->tmpDir.DIRECTORY_SEPARATOR.$fileName;

        // TODO: implement attachments
        $messageSent = $this->doCompose->doComposeMessage(
            $tmpFile,
            $email->to,
            $email->from,
            "Re: {$email->subject}",
            $emailAccount,
            $this->quoteReply($email),
            $params->arguments
        );

        if ($messageSent) {
            $this->service->setSeen($email);
            $this->service->setReplied($email);
        }
    }

    public function command(): string
    {
        return $this->command;
    }
}
