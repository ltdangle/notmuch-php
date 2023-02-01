<?php

namespace Dangle\Mailer\Service\Filesystem;

interface ReadFromFileInterface
{
    public function read(string $path): string;
}
