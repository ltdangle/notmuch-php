<?php

declare(strict_types=1);

namespace Dangle\Mailer\Model;

use Dangle\Mailer\Util\NumRange;
use Doctrine\Common\Collections\ArrayCollection;

class EmailCollection
{
    /**
     * @var ArrayCollection<Email>
     */
    private ArrayCollection $emails;

    public function __construct()
    {
        $this->emails = new ArrayCollection();
    }

    public function add(Email $email)
    {
        $this->emails->add($email);
    }

    /**
     * @return ArrayCollection<Email>
     */
    public function getAll(): ArrayCollection
    {
        return $this->emails;
    }

    /**
     * @return Email[]
     */
    public function get(NumRange $range): array
    {
        return $this->emails->slice($range->start(), $range->length());
    }
}
