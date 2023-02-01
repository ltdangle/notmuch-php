<?php

namespace Dangle\Mailer\Service\NotMuch;

/**
 * Wrapper around notmuch cli program.
 */
interface NotMuchInterface
{
    public function updateDb(): void;
}
