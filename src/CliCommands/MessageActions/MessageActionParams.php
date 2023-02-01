<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\MessageActions;

use Dangle\Mailer\Model\Email;
use Dangle\Mailer\Util\NumRange;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MessageActionParams
{
    public string $accAlias = '';
    public ?NumRange $messageRange = null;
    public bool $showHtml = false;
    /** @var Email[]|null */
    public array|null $emails = [];
    public ?InputInterface $input = null;
    public ?OutputInterface $output = null;
    public ?QuestionHelper $questionHelper = null;
    /** Extra cli arguments */
    public array $arguments = [];
}
