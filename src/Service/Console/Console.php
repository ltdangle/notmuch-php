<?php

declare(strict_types=1);

namespace Dangle\Mailer\Service\Console;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class Console implements ConsoleInterface
{
    private InputInterface $input;
    private OutputInterface $output;
    private QuestionHelper $questionHelper;

    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function ask(string $question): string
    {
        return $this->questionHelper->ask($this->input, $this->output, new Question($question));
    }

    public function writeLn(string $line)
    {
        $this->output->writeln($line);
    }
}
