<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service\FlagParser\test;

use Dangle\Mailer\Service\FlagParser\FlagParser;
use PHPUnit\Framework\TestCase;

class FlagParserTest extends TestCase
{
    /**
     * @test
     */
    public function itParsesFlagsCorrectly()
    {
        $data = [
            ['1666890028.7286_2.Dangles-MacBook-Pro,U=1:2,', []],
            ['1666976427.28751_1.Dangles-MacBook-Pro,U=14:2,S', ['S']],
            ['1666972827.27844_1.Dangles-MacBook-Pro,U=13:2,FRS', ['F', 'R', 'S']],
        ];
        foreach ($data as $item) {
            $this->_testParseFlagItem($item[0], $item[1]);
        }
    }

    private function _testParseFlagItem(string $path, array $expectedFlags)
    {
        $parser = new FlagParser($path);
        $this->assertEquals($expectedFlags, $parser->getFlags());
    }

    /**
     * @test
     */
    public function itSetsFlagsCorrectly()
    {
        $data = [
            ['1666890028.7286_2.Dangles-MacBook-Pro,U=1:2,', 'S', ['S'], '1666890028.7286_2.Dangles-MacBook-Pro,U=1:2,S'],
            ['1666976427.28751_1.Dangles-MacBook-Pro,U=14:2,S', 'F', ['F', 'S'], '1666976427.28751_1.Dangles-MacBook-Pro,U=14:2,FS'],
            ['1666972827.27844_1.Dangles-MacBook-Pro,U=13:2,FRS', 'F', ['F', 'R', 'S'], '1666972827.27844_1.Dangles-MacBook-Pro,U=13:2,FRS'],
        ];
        foreach ($data as $item) {
            $this->_testSetFlagItem($item[0], $item[1], $item[2], $item[3]);
        }
    }

    private function _testSetFlagItem(string $path, string $flag, array $expectedFlags, string $newPath)
    {
        $parser = new FlagParser($path);
        $parser->setFlag($flag);
        $this->assertEquals($expectedFlags, $parser->getFlags());
        $this->assertEquals($newPath, $parser->getPath());
    }

    /**
     * @test
     */
    public function itRemovesFlag()
    {
        $path = '1666976427.28751_1.Dangles-MacBook-Pro,U=14:2,S';
        $parser = new FlagParser($path);
        $parser->removeFlag('S');
        $this->assertEquals([], $parser->getFlags());
    }

    /**
     * @test
     *
     * @dataProvider pathProvider
     */
    public function itTogglesFlag(string $path, string $flag, string $expectedPath): void
    {
        $parser = new FlagParser($path);
        $parser->toggleFlag($flag);
        $this->assertEquals($expectedPath, $parser->getPath());
    }

    public function pathProvider(): array
    {
        return [
            ['1666890028.7286_2.Dangles-MacBook-Pro,U=1:2,', 'S', '1666890028.7286_2.Dangles-MacBook-Pro,U=1:2,S'],
            ['1666890028.7286_2.Dangles-MacBook-Pro,U=1:2,S', 'S', '1666890028.7286_2.Dangles-MacBook-Pro,U=1:2,'],
        ];
    }
}
