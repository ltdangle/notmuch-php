<?php

namespace Dangle\Mailer\Service\Filesystem;

interface SaveToFileInterface
{
    public function save(string $path, string $contents): void;
}
