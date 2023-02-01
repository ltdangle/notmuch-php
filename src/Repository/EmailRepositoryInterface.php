<?php

declare(strict_types=1);

namespace Dangle\Mailer\Repository;

use Dangle\Mailer\Model\EmailCollection;

interface EmailRepositoryInterface
{
    public function emails(string $accAlias): EmailCollection;
}
