<?php

namespace Chief\Decorators;

use Chief\Command;
use Chief\CommandBus;
use Chief\Decorator;

class EventDispatchingDecorator implements Decorator
{
    public function __construct(EventDispatcher $dispatcher, CommandBus $innerCommandBus = null)
    {
        $this->dispatcher = $dispatcher;
        $this->innerCommandBus = $innerCommandBus;
    }

    public function setInnerBus(CommandBus $bus)
    {
        $this->innerCommandBus = $bus;
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
    protected function getEventName(Command $command)
    {
        return str_replace('\\', '.', get_class($command));
    }
}