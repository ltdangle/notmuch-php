<?php

declare(strict_types=1);

namespace Dangle\Mailer\CliCommands\MessageActions;

class MessageActionsCollection
{
    /**
     * @var MessageActionInterface[]
     */
    private array $actions;

    public function __construct(iterable $actions)
    {
        $actions = $actions instanceof \Traversable ? \iterator_to_array($actions) : $actions;

        foreach ($actions as $command) {
            $this->add($command);
        }
    }

    public function add(MessageActionInterface $action)
    {
        $this->actions[] = $action;
    }

    public function executeAction(string $command, MessageActionParams $messageActionParams)
    {
        // retrieve the action
        $selectedAction = null;
        foreach ($this->actions as $action) {
            if ($command === $action->command()) {
                $selectedAction = $action;
                break;
            }
        }
        if (!$selectedAction) {
            throw new \InvalidArgumentException("Command $command is not registered with message action commands.");
        }

        $action->execute($messageActionParams);
    }
}
