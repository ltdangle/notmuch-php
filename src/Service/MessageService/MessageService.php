<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service\MessageService;

use Dangle\Mailer\Model\Email;
use Dangle\Mailer\Model\EmailCollection;
use Dangle\Mailer\Repository\EmailRepositoryInterface;
use Dangle\Mailer\Service\Filesystem\MoveFileInterface;
use Dangle\Mailer\Service\Filesystem\RenameFileInterface;
use Dangle\Mailer\Service\FlagParser\FlagParser;
use Dangle\Mailer\Service\NotMuch\NotMuchInterface;
use Dangle\Mailer\Settings;

class MessageService implements MessageServiceInterface
{
    private MoveFileInterface $moveFile;
    private RenameFileInterface $renameFile;
    private EmailRepositoryInterface $emailRepository;
    private NotMuchInterface $notMuch;
    private Settings $settings;

    public function __construct(MoveFileInterface $moveFile, RenameFileInterface $renameFile, EmailRepositoryInterface $emailRepository, NotMuchInterface $notMuch, Settings $settings)
    {
        $this->moveFile = $moveFile;
        $this->renameFile = $renameFile;
        $this->emailRepository = $emailRepository;
        $this->notMuch = $notMuch;
        $this->settings = $settings;
    }

    public function emails(string $accAlias): EmailCollection
    {
        return $this->emailRepository->emails($accAlias);
    }

    public function toggleFlag(Email $email): void
    {
        $oldPath = $email->path;

        $flagParser = new FlagParser($oldPath);
        $flagParser->toggleFlag(FlagParser::FLAG_FLAGGED);
        $newPath = $flagParser->getPath();

        $this->renameFile->rename($oldPath, $newPath);

        $email->path = $newPath;

        $email->isImportant = !$email->isImportant;

        $this->notMuch->updateDb();
    }

    public function toggleSeen(Email $email): void
    {
        $oldPath = $email->path;

        $flagParser = new FlagParser($oldPath);
        $flagParser->toggleFlag(FlagParser::FLAG_SEEN);
        $newPath = $flagParser->getPath();

        $this->renameFile->rename($oldPath, $newPath);

        $email->path = $newPath;

        $email->isSeen = !$email->isSeen;

        $this->notMuch->updateDb();
    }

    public function setSeen(Email $email)
    {
        $oldPath = $email->path;

        $flagParser = new FlagParser($oldPath);
        if (in_array(FlagParser::FLAG_SEEN, $flagParser->getFlags(), true)) {
            return;
        }

        $flagParser->setFlag(FlagParser::FLAG_SEEN);
        $newPath = $flagParser->getPath();

        $this->renameFile->rename($oldPath, $newPath);

        $email->path = $newPath;

        $this->notMuch->updateDb();
    }

    public function setReplied(Email $email)
    {
        $oldPath = $email->path;

        $flagParser = new FlagParser($oldPath);
        if (in_array(FlagParser::FLAG_REPLIED, $flagParser->getFlags(), true)) {
            return;
        }

        $flagParser->setFlag(FlagParser::FLAG_REPLIED);
        $newPath = $flagParser->getPath();

        $this->renameFile->rename($oldPath, $newPath);

        $email->path = $newPath;

        $this->notMuch->updateDb();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(array $emails): void
    {
        // move emails to 'deleted' folder
        foreach ($emails as $email) {
            $acc = $this->settings->getAccountByEmail($email->deliveredTo);
            if (!$acc) {
                throw new AccountNotFoundException("Account {$email->to} not found, cannot move deleted file.");
            }
            if (!$acc->trashFolder) {
                throw new DeletedFolderNotConfiguredException("Deleted folder for {$acc->shortName} is not configured, cannot move deleted file.");
            }

            $pathArr = explode(DIRECTORY_SEPARATOR, $email->path);
            $filename = array_pop($pathArr);

            $deletedPath = $acc->trashFolder.DIRECTORY_SEPARATOR.$filename;
            $this->moveFile->move($email->path, $deletedPath);
        }

        $this->notMuch->updateDb();
    }
}
