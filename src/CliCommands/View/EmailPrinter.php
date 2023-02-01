<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\View;

use Dangle\Mailer\Model\Email;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class EmailPrinter
{
    /**
     * @param Email[] $emails
     */
    public function show(OutputInterface $output, array|ArrayCollection $emails): void
    {
        // display table
        $table = new Table($output);
        $table->setHeaders(['TO', 'F', 'ID', 'SUBJECT', 'SENDER', 'DATE']);

        // build and display emails in a table
        $counter = 0;
        foreach ($emails as $email) {
            $this->_buildTable($counter, $email, $table);
            ++$counter;
        }
        $table->render();
    }

    private function _buildTable(int $counter, Email $email, Table $table)
    {
        $r = new EmailRow($email);

        $options = $r->isSeen() ? '' : 'bold';
        $unread = $r->isSeen() ? '' : '*';
        $bg = $r->isSelected() ? ';bg=#ffffff' : '';
        $important = $r->isImportant() ? '!' : '';
        $replied = $r->isAnswered() ? 'R' : '';
        $table->addRow([
            "<fg=#31BD25{$bg};options=$options>{$r->getTo()}</>",
            "<fg=#31BD25{$bg};options=$options>{$unread} {$important} {$replied}</>",
            "<fg=#C23820{$bg};options=$options>$counter</>",
            "<fg=#31BD25{$bg};options=$options>{$r->getSubject()}</>",
            "<fg=#31BD25{$bg};options=$options>{$r->getFrom()}</>",
            "<fg=#AEAD25{$bg};options=$options>{$r->getDate()}</>",
        ]);
    }
}
