<?php

declare(strict_types=1);

namespace Dangle\Mailer\Model;

class EmailAccount
{
    /**
     * 'Regular' Maildir-based email account or 'virtual' (i.e. notmuch search).
     */
    public string $accountType = '';
    public string $shortName = '';
    public string $email = '';
    public string $inboxShellCommand = '';
    public string $trashFolder = '';
    /**
     * Email delivery transport settings.
     */
    public string $dsn = '';

    public function toArray(): array
    {
        $arr = [];
        foreach ($this as $key => $value) {
            $arr[$key] = $value;
        }

        return $arr;
    }
}
