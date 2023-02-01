<?php

declare(strict_types=1);

namespace Dangle\Mailer\Util\tests;

use Dangle\Mailer\Util\NumRange;
use Dangle\Mailer\Util\RegexParser;
use PHPUnit\Framework\TestCase;

class RegexParserTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider dataProvider
     */
    public function itParsesNumRange(string $string, mixed $result)
    {
        $regex = new RegexParser();
        $range = $regex->numRange($string);
        $this->assertEquals($range, $result);
    }

    public function dataProvider(): array
    {
        return [
            ['3-9', new NumRange(3, 9)],
            ['33-98', new NumRange(33, 98)],
            ['333-777', new NumRange(333, 777)],
            ['x3-43', null],
            ['xx-yy', null],
            ['3--9', null],
            ['18-kfk-44', null],
            ['1', new NumRange(1, 1)],
            ['273', new NumRange(273, 273)],
        ];
    }
}
