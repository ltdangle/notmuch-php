<?php

declare(strict_types=1);

namespace Dangle\Mailer\Util;

class NumRange
{
    private int $start;
    private int $end;

    public function __construct(int $start, int $end)
    {
        if ($start > $end) {
            throw new \InvalidArgumentException("Range $start - $end is invalid. End canno be larger then start.");
        }
        $this->start = $start;
        $this->end = $end;
    }

    public function start(): int
    {
        return $this->start;
    }

    public function end(): int
    {
        return $this->end;
    }

    public function isSingleDigit(): bool
    {
        if ($this->start() === $this->end()) {
            return true;
        }

        return false;
    }

    public function length(): int
    {
        return $this->end() - $this->start() + 1;
    }
}
