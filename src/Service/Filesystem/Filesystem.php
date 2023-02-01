<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service\Filesystem;

use Dangle\Mailer\Events\FilesystemChangedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Filesystem implements SaveToFileInterface, ReadFromFileInterface, MoveFileInterface, RenameFileInterface, DeleteFileInterface
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function rename(string $oldPath, string $newPath): void
    {
        rename($oldPath, $newPath);
        $this->eventDispatcher->dispatch(new FilesystemChangedEvent(), FilesystemChangedEvent::NAME);
    }

    public function delete(string $path): void
    {
        unlink($path);
        $this->eventDispatcher->dispatch(new FilesystemChangedEvent(), FilesystemChangedEvent::NAME);
    }

    public function move(string $fromPath, string $toPath)
    {
        rename($fromPath, $toPath);
    }

    public function read(string $path): string
    {
        $contents = file_get_contents($path);
        if (false === $contents) {
            throw new FilesystemException("Cannot read file $path.");
        }

        return $contents;
    }

    public function save(string $path, string $contents): void
    {
        $fileSaved = file_put_contents($path, $contents);
        if (false === $fileSaved) {
            throw new FilesystemException("Could not write to $path");
        }
    }
}
