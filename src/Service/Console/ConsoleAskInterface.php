<?php

namespace Dangle\Mailer\Service\Console;

interface ConsoleAskInterface
{
    /**
     * Returns answer to the question.
     */
    public function ask(string $question): string;
}
