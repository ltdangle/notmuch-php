<?php

declare(strict_types=1);

namespace Dangle\Mailer\Util;

use Spatie\Regex\Regex;

class RegexParser
{
    /**
     * Parses range of numbers in the format 'number-number'.
     */
    public function numRange(string $str): ?NumRange
    {
        if ($this->twoNumbersRange($str)) {
            return $this->twoNumbersRange($str);
        }

        if ($this->oneNumberRange($str)) {
            return $this->oneNumberRange($str);
        }

        return null;
    }

    private function oneNumberRange(string $str): ?NumRange
    {
        $pattern = "/^(\d+)$/";

        $match = Regex::match($pattern, $str);
        if (!$match->hasMatch()) {
            return null;
        }

        $num = (int) $match->group(1);

        return new NumRange($num, $num);
    }

    private function twoNumbersRange(string $str): ?NumRange
    {
        $pattern = "/^(\d+)-(\d+)$/";

        $match = Regex::match($pattern, $str);
        if (!$match->hasMatch()) {
            return null;
        }

        $start = (int) $match->group(1);
        $end = (int) $match->group(2);

        return new NumRange($start, $end);
    }
}
