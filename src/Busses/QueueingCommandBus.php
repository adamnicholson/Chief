<?php

namespace Chief\Busses;

use Chief\Command;
use Chief\CommandBus;
use Chief\CommandBusQueuer;
use Chief\QueueableCommand;

/**
 * QueueingCommandBus attempts to queue commands into a CommandBusQueuer,
 * for execution at a deferred time. Only Commands which implement
 * QueueableCommand will be added to the queue; else they will be forwarded
 * to the inner CommandBus
 */
class QueueingCommandBus implements CommandBus
{
    public function __construct(CommandBusQueuer $queuer, CommandBus $innerBus = null)
    {
        $this->queuer = $queuer;
        $this->innerBus = $innerBus ?: new SynchronousCommandBus();
    }

    /**
     * Execute a command
     *
     * @param Command $command
     * @return void
     */
    public function execute(Command $command)
    {
        if ($command instanceof QueueableCommand) {
            $this->queuer->queue($command);
            return;
        }

        $this->innerBus->execute($command);
    }
}
