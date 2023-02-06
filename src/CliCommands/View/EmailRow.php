<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\View;

use Dangle\Mailer\Model\Email;

/**
 * Wrapper around @see Email for console table output.
 */
class EmailRow
{
    private Email $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function getFrom(): string
    {
        if (strlen($this->email->from) > 20) {
            return substr($this->email->from, 0, 20).'...';
        }

        return $this->email->from;
    }

    public function getDeliveredTo(): string
    {
        if (strlen($this->email->deliveredTo) > 20) {
            return substr($this->email->deliveredTo, 0, 20).'...';
        }

        return $this->email->deliveredTo;
    }

    public function getSubject(): string
    {
        // truncate subject string
        if (strlen($this->email->subject) > 30) {
            $this->email->subject = substr($this->email->subject, 0, 30).'...';
        }

        $flags = '';
        if ($this->email->text) {
            $flags = 'T';
        }
        // indicate if email has html part
        if ($this->email->html) {
            $flags .= 'H';
        }

        return $this->email->subject." <$flags>";
    }

    public function getDate(): string
    {
        try {
            $formattedDate = date('d M Y', strtotime($this->email->date));

            return substr($formattedDate, 0, 15);
        } catch (\Throwable $e) {
            return 'n/a';
        }
    }

    public function getPath(): string
    {
        return $this->email->path;
    }

    public function isSeen(): bool
    {
        return $this->email->isSeen;
    }

    public function isImportant(): bool
    {
        return $this->email->isImportant;
    }

    public function isAnswered(): bool
    {
        return $this->email->isAnswered;
    }

    public function isSelected(): bool
    {
        return $this->email->isSelected;
    }
}
