<?php

namespace Chief\Decorators;

use Chief\Command;
use Chief\CommandBus;

class EventDispatchingDecorator implements CommandBus
{
    public function __construct(EventDispatcher $dispatcher, CommandBus $innerCommandBus)
    {
        $this->dispatcher = $dispatcher;
        $this->innerCommandBus = $innerCommandBus;
    }

    /**
     * Execute a command and dispatch and event
     *
     * @param Command $command
     * @return void
     */
    public function execute(Command $command)
    {
        $this->innerCommandBus->execute($command);

        $eventName = $this->getEventName($command);

        $this->dispatcher->dispatch($eventName, [$command]);
    }

    /**
     * Get the event name for a given Command
     *
     * @param Command $command
     * @return string
     */
    protected function getEventName (Command $command)
    {
        return str_replace('\\', '.', get_class($command));
    }
}