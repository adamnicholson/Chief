<?php

namespace Chief\Busses;

use Chief\Command;
use Chief\CommandBus;

/**
 * TransactionalCommandBus treats commands as transactions. Meaning that any
 * subsequent Commands passed to the bus from inside the relevant CommandHandler
 * will not be executed until the initial command is completed.
 */
class TransactionalCommandBus implements CommandBus
{
    protected $locked = false;
    protected $queue = [];

    public function __construct(CommandBus $innerBus = null)
    {
        $this->innerBus = $innerBus ?: new SynchronousCommandBus();
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
        return $this->innerBus->execute($command);
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
