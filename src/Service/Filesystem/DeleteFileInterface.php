<?php

namespace Dangle\Mailer\Service\Filesystem;

interface DeleteFileInterface
{
    public function delete(string $path): void;
}
