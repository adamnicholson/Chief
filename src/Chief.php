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
     * @param array $decorators Array of \Chief\Decorator objects
     * @throws \InvalidArgumentException when invalid decorators are passed
     */
    public function __construct(CommandBus $bus = null, array $decorators = [])
    {
        $this->bus = $bus ?: new SynchronousCommandBus;

        foreach ($decorators as $decorator) {
            if (!$decorator instanceof Decorator) {
                throw new \InvalidArgumentException('Decorators must implement [Chief\Decorator]');
            }

            $decorator->setInnerBus($this->bus);

            $this->bus = $decorator;
        }
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