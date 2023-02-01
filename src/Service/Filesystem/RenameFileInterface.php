<?php

namespace Dangle\Mailer\Service\Filesystem;

interface RenameFileInterface
{
    public function rename(string $oldPath, string $newPath): void;
}
