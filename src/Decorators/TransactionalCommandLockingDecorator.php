<?php

namespace Chief\Decorators;

use Chief\Busses\SynchronousCommandBus;
use Chief\Command;
use Chief\CommandBus;
use Chief\Decorator;

/**
 * TransactionalCommandLockingDecorator treats commands as transactions. Meaning that any
 * subsequent Commands passed to the bus from inside the relevant CommandHandler
 * will not be executed until the initial command is completed.
 */
class TransactionalCommandLockingDecorator implements Decorator
{
    use InnerBusTrait;

    /**
     * Whether or not a Command is in progress and the bus is locked
     * @var bool
     */
    protected $locked = false;

    /**
     * Queued Commands to be executed when the current command finishes
     * @var array
     */
    protected $queue = [];

    public function __construct(CommandBus $innerCommandBus = null)
    {
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
        if ($this->locked === true) {
            $this->queue[] = $command;
            return null;
        }

        $this->locked = true;

        $response = $this->executeIgnoringLock($command);

        $this->executeQueue();

        $this->locked = false;

        return $response;
    }

    /**
     * Execute a command, regardless of the lock
     *
     * @param Command $command
     * @return mixed
     */
    protected function executeIgnoringLock(Command $command)
    {
        return $this->innerCommandBus->execute($command);
    }

    /**
     * Execute all queued commands
     */
    protected function executeQueue()
    {
        foreach ($this->queue as $command) {
            $this->executeIgnoringLock($command);
        }
    }
}
