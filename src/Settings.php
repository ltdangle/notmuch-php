<?php

declare(strict_types=1);

namespace Dangle\Mailer;

use Dangle\Mailer\Model\EmailAccount;

class Settings
{
    /**
     * @var EmailAccount[]
     */
    private array $emailAccounts = [];

    /**
     * @return EmailAccount[]
     */
    public function getEmailAccounts(): array
    {
        return $this->emailAccounts;
    }

    public function addAccount(EmailAccount $emailAccount)
    {
        $this->emailAccounts[] = $emailAccount;
    }

    public function getAccountByAlias(string $shortName): ?EmailAccount
    {
        foreach ($this->emailAccounts as $account) {
            if ($shortName === $account->shortName) {
                return $account;
            }
        }

        return null;
    }

    public function getAccountByEmail(string $email): ?EmailAccount
    {
        foreach ($this->emailAccounts as $account) {
            if (strtolower($email) === strtolower($account->email)) {
                return $account;
            }
        }

        return null;
    }

    public function toArray(): array
    {
        $arr = [];
        foreach ($this->emailAccounts as $account) {
            $arr[] = $account->toArray();
        }

        return $arr;
    }
}
