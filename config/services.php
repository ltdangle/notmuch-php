<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Dangle\Mailer\App;
use Dangle\Mailer\CliCommands\Actions\ComposeMessage;
use Dangle\Mailer\CliCommands\Actions\ReadMessage\ReadMessageHtml;
use Dangle\Mailer\CliCommands\MessageActions\DeleteMessageAction;
use Dangle\Mailer\CliCommands\MessageActions\EmailPathAction;
use Dangle\Mailer\CliCommands\MessageActions\MessageActionInterface;
use Dangle\Mailer\CliCommands\MessageActions\MessageActionsCollection;
use Dangle\Mailer\CliCommands\MessageActions\ReadMessage\ReadMessageAction;
use Dangle\Mailer\CliCommands\MessageActions\ReadMessage\ReadMessageAsHtmlAction;
use Dangle\Mailer\CliCommands\MessageActions\ReplyMessageAction;
use Dangle\Mailer\CliCommands\MessageActions\ToggleFlagAction;
use Dangle\Mailer\CliCommands\MessageActions\ToggleSeenAction;
use Dangle\Mailer\Events\ConsoleNotificationEventListener;
use Dangle\Mailer\Events\FilesystemChangedEventListener;
use Dangle\Mailer\Factory\SettingsFactory;
use Dangle\Mailer\Repository\EmailRepositoryInterface;
use Dangle\Mailer\Repository\FilesystemEmailRepository;
use Dangle\Mailer\Repository\StdinEmailRepository;
use Dangle\Mailer\Service\Console\ConsoleInput;
use Dangle\Mailer\Service\Console\ConsoleOutput;
use Dangle\Mailer\Service\Console\ConsoleQuestionHelper;
use Dangle\Mailer\Service\EmailTransport\EmailTransportInterface;
use Dangle\Mailer\Service\EmailTransport\SymfonyEmailTransport;
use Dangle\Mailer\Settings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

return static function (ContainerConfigurator $configurator) {
    $tmpDir = 'tmp';
    $tmpFile = 'tmp/compose.txt';

    $services = $configurator
        ->services()
        ->defaults()
        ->autowire();

    // tag all instances of Command class
    $services->instanceof(Command::class)
        ->tag('app.command');

    // tag all instances of MessageActionInterface class
    $services->instanceof(MessageActionInterface::class)
        ->tag('app.message.actions');


    // autoload all services in namespace
    $services->load('Dangle\Mailer\\', '../src/*')->exclude('../src/{Model}');

    // pass all instances of Command class to the application constructor
    $services->set(App::class)
        ->public()
        ->args([tagged_iterator('app.command')]);

    // pass all instances of MessageActionInterface class to the MessageActionsCollection
    $services->set(MessageActionsCollection::class)
        ->args([tagged_iterator('app.message.actions')]);

    // set up event dispatcher
    $services->set(EventDispatcherInterface::class, EventDispatcher::class)->public();
    $services->set(FilesystemChangedEventListener::class)->public();
    $services->set(ConsoleNotificationEventListener::class)->public();

    // set up individual services
    $services->set(EmailTransportInterface::class, SymfonyEmailTransport::class);
    $services->set(Settings::class)->factory(service(SettingsFactory::class));

    //set up message actions
    $services->set(ComposeMessage::class)->arg('$tmpFile', $tmpFile);
    $services->set(ReadMessageAction::class)->arg('$command', 'r')->arg('$tmpDir', $tmpDir);
    $services->set(ReadMessageAsHtmlAction::class)->arg('$command', 'rh');
    $services->set(ReplyMessageAction::class)->arg('$command', 'rp')->arg('$tmpDir', $tmpDir);
    $services->set(DeleteMessageAction::class)->arg('$command', 'd');
    $services->set(ToggleFlagAction::class)->arg('$command', 'f');
    $services->set(ToggleSeenAction::class)->arg('$command', 's');
    $services->set(EmailPathAction::class)->arg('$command', 'path');
    $services->set(FilesystemEmailRepository::class)->arg('$stdinAccAlias', 'stdin')->arg('$window', $_ENV["MESSAGE_WINDOW_SIZE"]);

    $services->alias(OutputInterface::class, ConsoleOutput::class);
    $services->alias(InputInterface::class, ConsoleInput::class);
    $services->alias(QuestionHelper::class, ConsoleQuestionHelper::class);
    $services->alias(EmailRepositoryInterface::class, FilesystemEmailRepository::class);
};
