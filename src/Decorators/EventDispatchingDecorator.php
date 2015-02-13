<?php

namespace Chief\Decorators;

use Chief\Busses\SynchronousCommandBus;
use Chief\Command;
use Chief\CommandBus;
use Chief\Decorator;
use Exception;

class EventDispatchingDecorator implements Decorator
{
    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var CommandBus
     */
    protected $innerCommandBus;

    /**
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher, CommandBus $innerCommandBus = null)
    {
        $this->dispatcher = $dispatcher;
        $this->setInnerBus($innerCommandBus ?: new SynchronousCommandBus());
    }

    public function setInnerBus(CommandBus $bus)
    {
        $this->innerCommandBus = $bus;
    }

    /**
     * Execute a command and dispatch and event
     *
     * @param Command $command
     * @return mixed
     * @throws \Exception
     */
    public function execute(Command $command)
    {
        if (!$this->innerCommandBus) {
            throw new Exception('No inner bus defined for this decorator. Set an inner bus with setInnerBus()');
        }

        $response = $this->innerCommandBus->execute($command);

        $eventName = $this->getEventName($command);

        $this->dispatcher->dispatch($eventName, [$command]);

        return $response;
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
