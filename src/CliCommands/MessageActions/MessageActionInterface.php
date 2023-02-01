<?php

namespace Dangle\Mailer\CliCommands\MessageActions;

interface MessageActionInterface
{
    /**
     * Key command to execute this action.
     */
    public function command(): string;

    public function execute(MessageActionParams $params): void;
}
