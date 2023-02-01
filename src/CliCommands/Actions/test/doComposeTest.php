<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\Actions\test;

use Dangle\Mailer\CliCommands\Actions\doCompose;
use Dangle\Mailer\Model\EmailAccount;
use Dangle\Mailer\Model\OutgoingEmail;
use Dangle\Mailer\Service\Console\ConsoleAskInterface;
use Dangle\Mailer\Service\EmailTransport\EmailTransportInterface;
use Dangle\Mailer\Service\Filesystem\ReadFromFileInterface;
use Dangle\Mailer\Service\Filesystem\SaveToFileInterface;
use Dangle\Mailer\Service\Process\ProcessInterface;
use Dangle\Mailer\Service\Process\ProcessTtyInterface;
use PHPUnit\Framework\TestCase;

class doComposeTest extends TestCase
{
    /**
     * @test
     */
    public function itRuns()
    {
        $emailTransport = new class() implements EmailTransportInterface {
            public function send(OutgoingEmail $email)
            {
            }
        };

        $consoleAsk = new class() implements ConsoleAskInterface {
            public function ask(string $question): string
            {
                return 'y';
            }
        };

        $processTty = new class() implements ProcessTtyInterface {
            public function run(string $command): void
            {
            }
        };

        $process = new class() implements ProcessInterface {
            public function run(string $command): void
            {
            }

            public function getOutput(): string
            {
                return '';
            }

            public function getExitCode(): ?int
            {
                return 1;
            }
        };

        $fileSystem = new class() implements SaveToFileInterface, ReadFromFileInterface {
            private string $tmpFileContents = '';

            public function read(string $path): string
            {
                return $this->tmpFileContents;
            }

            public function save(string $path, string $contents): void
            {
                $this->tmpFileContents = $contents;
            }
        };

        $c = new doCompose($emailTransport, $consoleAsk, $processTty, $process, $fileSystem, $fileSystem);
        $c->doComposeMessage('/tmp/tmpfile.txt', 'from', 'to', 'subject', new EmailAccount(), 'message');
        $this->markTestSkipped('Skipped');
    }
}
