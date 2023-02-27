<?php

declare(strict_types=1);

namespace Dangle\Mailer\Repository;

use Dangle\Mailer\Model\Email;
use Dangle\Mailer\Model\EmailCollection;
use Dangle\Mailer\Service\Console\StdInInterface;
use Dangle\Mailer\Service\EmailFileParser;
use Dangle\Mailer\Service\Process\ProcessInterface;
use Dangle\Mailer\Settings;

class FilesystemEmailRepository implements EmailRepositoryInterface
{
    private int $offset = 0;
    private int $window;
    private string $shellCommand = '';
    private ?string $stdin = null;

    private Settings $settings;
    private EmailFileParser $parseEmailFile;
    private ProcessInterface $process;
    private StdInInterface $stdInReader;
    private string $stdinAccAlias;

    public function __construct(Settings $settings, EmailFileParser $parseEmailFile, ProcessInterface $process, StdInInterface $stdInReader, string $stdinAccAlias, int $window)
    {
        $this->settings = $settings;
        $this->parseEmailFile = $parseEmailFile;
        $this->process = $process;
        $this->stdInReader = $stdInReader;
        $this->stdinAccAlias = $stdinAccAlias;
        $this->window = $window;
    }

    public function emails(string $accAlias): EmailCollection
    {
        if ($accAlias === $this->stdinAccAlias) {
            $paths = $this->emailsFromStdin();
        } else {
            $paths = $this->emailsFromConfiguredAccounts($accAlias);
        }

        $sanitizedPaths = $this->sanitizePaths($paths);

        return $this->buildEmailCollection($sanitizedPaths);
    }

    private function buildEmailCollection(array $paths)
    {
        $emails = new EmailCollection();
        $window = array_slice($paths, $this->getOffset(), $this->getWindow());
        foreach ($window as $emailPath) {
            try {
                $email = $this->parseEmailFile->parse($emailPath);
            } // email could not be parsed
            catch (\Throwable $e) {
                $email = new Email();
                $email->path = 'n/a';
                $email->from = 'n/a';
                $email->to = 'n/a';
                $email->subject = 'n/a';
                $email->date = 'n/a';
                $email->text = 'n/a';
            }
            $emails->add($email);
        }

        return $emails;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getWindow(): int
    {
        return $this->window;
    }

    public function setWindow(int $window): void
    {
        $this->window = $window;
    }

    private function sanitizePaths($paths): array
    {
        $sanitizedPaths = [];
        foreach ($paths as $path) {
            // skip files that start with dot (.)
            $pathArr = explode(DIRECTORY_SEPARATOR, $path);
            $fileName = $pathArr[count($pathArr) - 1];
            if (!$fileName || '.' === $fileName[0]) {
                continue;
            }
            $sanitizedPaths[] = $path;
        }

        return $sanitizedPaths;
    }

    public function getShellCommand(): string
    {
        return $this->shellCommand;
    }

    public function setShellCommand(string $shellCommand): void
    {
        $this->shellCommand = $shellCommand;
    }

    private function emailsFromConfiguredAccounts(string $accAlias): array
    {
        $acc = $this->settings->getAccountByAlias($accAlias);
        if (!$acc) {
            throw new \InvalidArgumentException("Account $accAlias is not configured");
        }

        $this->process->run($acc->inboxShellCommand);
        if (0 !== $this->process->getExitCode()) {
            throw new \RuntimeException("Command '{$this->getShellCommand()}' returned non-zero code.");
        }

        return explode("\n", $this->process->getOutput());
    }

    private function emailsFromStdin(): array
    {
        if (!$this->stdin) {
            $this->stdin = $this->stdInReader->getInput();
        }

        return explode("\n", $this->stdin);
    }
}
