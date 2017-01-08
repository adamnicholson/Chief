<?php

namespace Chief\Decorator\Queue;

use Chief\SynchronousCommandBus;
use Chief\Command;
use Chief\CommandBus;
use Chief\Decorator;
use Chief\Decorator\InnerBusTrait;

/**
 * Queue commands which implement QueueableCommand into a CommandQueuer
 */
class QueueingDecorator implements Decorator
{
    use InnerBusTrait;

    /**
     * @var \Chief\Decorator\Queue\CommandQueuer
     */
    protected $queuer;

    /**
     * @param CommandQueuer $queuer
     * @param CommandBus $innerCommandBus
     */
    public function __construct(CommandQueuer $queuer, CommandBus $innerCommandBus = null)
    {
        $this->queuer = $queuer;
        $this->setInnerBus($innerCommandBus ?: new SynchronousCommandBus());
    }

    /**
     * Execute a command
     *
     * @param Command $command
     * @return mixed
     */
    public function execute(Command $command)
    {
        if ($command instanceof QueueableCommand) {
            $this->queuer->queue($command);
            return null;
        }

        return $this->innerCommandBus->execute($command);
    }
}
