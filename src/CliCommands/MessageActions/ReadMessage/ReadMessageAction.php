<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\MessageActions\ReadMessage;

use Dangle\Mailer\CliCommands\MessageActions\MessageActionInterface;
use Dangle\Mailer\CliCommands\MessageActions\MessageActionParams;
use Dangle\Mailer\CliCommands\MessageActions\ReadMessage\EmailViewers\HtmlMailViewer;
use Dangle\Mailer\CliCommands\MessageActions\ReadMessage\EmailViewers\TextMailViewer;
use Dangle\Mailer\Service\MessageService\MessageServiceInterface;

class ReadMessageAction implements MessageActionInterface
{
    private string $command;
    private MessageServiceInterface $service;
    private string $tmpDir;
    protected TextMailViewer $textMailViewer;
    private HtmlMailViewer $htmlMailViewer;

    public function __construct(string $command, MessageServiceInterface $service, string $tmpDir, TextMailViewer $textMailViewer, HtmlMailViewer $htmlMailViewer)
    {
        $this->service = $service;
        $this->tmpDir = $tmpDir;
        $this->textMailViewer = $textMailViewer;
        $this->htmlMailViewer = $htmlMailViewer;
        $this->command = $command;
    }

    public function execute(MessageActionParams $params): void
    {
        if (!$params->messageRange->isSingleDigit()) {
            throw new \InvalidArgumentException('You can read only one message at a time');
        }
        $emails = $this->service->emails($params->accAlias)->get($params->messageRange);
        $email = array_pop($emails);
        $this->service->setSeen($email);

        // save parsed message to temp file
        $fileNameArr = explode(DIRECTORY_SEPARATOR, $email->path);
        $fileName = 'reply-'.$fileNameArr[count($fileNameArr) - 1];
        $tmpFile = $this->tmpDir.DIRECTORY_SEPARATOR.$fileName;

        // optionally show html version
        if ($params->showHtml) {
            $this->htmlMailViewer->view($email, $tmpFile);
        }

        // show text version
        $this->textMailViewer->view($email, $tmpFile);
    }

    public function command(): string
    {
        return $this->command;
    }
}
