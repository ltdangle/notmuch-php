<?php

namespace Dangle\Mailer\Service\Filesystem;

interface MoveFileInterface
{
    public function move(string $fromPath, string $toPath);
}
