<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service\FlagParser;

/**
 * Maildir format: https://cr.yp.to/proto/maildir.html.
 */
class FlagParser
{
    public const FLAG_SEEN = 'S';
    public const FLAG_REPLIED = 'R';
    public const FLAG_FLAGGED = 'F';

    /**
     * Email file path.
     */
    private string $path;

    /**
     * Array of email flags.
     */
    private array $flags = [];

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->_parseFlags();
    }

    private function _parseFlags()
    {
        // get flags string after last comma
        $parts = explode(',', $this->path);
        $flagsStr = $parts[count($parts) - 1];

        // parse flags string into array
        $flagCount = strlen($flagsStr);
        for ($i = 0; $i < $flagCount; ++$i) {
            $this->flags[] = $flagsStr[$i];
        }
    }

    public function getFlags(): array
    {
        return $this->flags;
    }

    public function toggleFlag(string $flag)
    {
        if (in_array($flag, $this->flags, true)) {
            $this->removeFlag($flag);

            return;
        }
        $this->setFlag($flag);
    }

    public function setFlag(string $flag): void
    {
        if (strlen($flag) > 1) {
            throw new \InvalidArgumentException('Flag must be one character long');
        }

        if (in_array($flag, $this->flags, true)) {
            return;
        }

        $this->flags[] = $flag;

        // sort values in alphabetical order as required by spec
        sort($this->flags);

        $this->buildPath();
    }

    public function removeFlag(string $flag)
    {
        if (!in_array($flag, $this->flags, true)) {
            return;
        }

        $flagKey = array_search($flag, $this->flags, true);

        unset($this->flags[$flagKey]);

        // re-index flags array
        $this->flags = array_values($this->flags);

        $this->buildPath();
    }

    public function getPath(): string
    {
        return $this->path;
    }

    private function buildPath(): void
    {
        // create flag string
        $flagsStr = implode('', $this->flags);

        // update path with new flag string
        $pathParts = explode(',', $this->path);
        $pathParts[count($pathParts) - 1] = $flagsStr;
        $this->path = implode(',', $pathParts);
    }
}
