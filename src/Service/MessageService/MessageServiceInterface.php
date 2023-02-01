<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service\MessageService;

use Dangle\Mailer\Model\Email;
use Dangle\Mailer\Model\EmailCollection;

interface MessageServiceInterface
{
    public function emails(string $accAlias): EmailCollection;

    public function setReplied(Email $email);

    public function setSeen(Email $email);

    public function toggleSeen(Email $email);

    public function toggleFlag(Email $email): void;

    /**
     * Moves emails to 'deleted' folder for the account.
     *
     * @param Email[] $emails
     *
     * @throws DeletedFolderNotConfiguredException
     */
    public function delete(array $emails): void;
}
