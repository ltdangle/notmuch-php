<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service;

use Dangle\Mailer\Model\Email;
use Dangle\Mailer\Service\Filesystem\ReadFromFileInterface;
use Dangle\Mailer\Service\FlagParser\FlagParser;
use ZBateson\MailMimeParser\Message;

class EmailFileParser
{
    private ReadFromFileInterface $readFromFile;

    public function __construct(ReadFromFileInterface $readFromFile)
    {
        $this->readFromFile = $readFromFile;
    }

    public function parse(string $path): Email
    {
        $m = Message::from($this->readFromFile->read($path), true);

        $email = new Email();
        $email->path = $path;
        $email->from = (string) $m->getHeaderValue('From');
        $email->to = (string) $m->getHeaderValue('To');
        $email->deliveredTo = (string) $m->getHeaderValue('Delivered-To');
        $email->subject = (string) $m->getHeaderValue('Subject');
        $email->date = (string) $m->getHeaderValue('Date');
        $email->text = (string) $m->getTextContent();
        $email->html = (string) $m->getHtmlContent();
        $this->parseFlags($path, $email);

        return $email;
    }

    private function parseFlags(string $path, Email $email)
    {
        $pathArr = explode(',', $path);
        $flags = $pathArr[count($pathArr) - 1];
        // TODO: use FlagParser
        if (str_contains($flags, FlagParser::FLAG_SEEN)) {
            $email->isSeen = true;
        }
        if (str_contains($flags, FlagParser::FLAG_REPLIED)) {
            $email->isAnswered = true;
        }
        if (str_contains($flags, FlagParser::FLAG_FLAGGED)) {
            $email->isImportant = true;
        }
    }
}
