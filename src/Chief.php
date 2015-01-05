<?php

namespace Chief;

use Chief\Busses\SynchronousCommandBus;

/**
 * The main Chief class is a CommandBus, which is effectively a decorator
 * around another CommandBus interface
 *
 */
class Chief implements CommandBus
{
    /**
     * Constructor
     *
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus = null)
    {
        $this->bus = $bus ?: new SynchronousCommandBus();
    }

    /**
     * Execute a command
     *
     * @param Command $command
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function execute(Command $command)
    {
        return $this->bus->execute($command);
    }
}