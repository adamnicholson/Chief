<?php

namespace Chief;

interface CommandBus
{
    /**
     * Execute a command
     *
     * @param Command $command
     * @return mixed
     */
    public function execute(Command $command);

    /**
     * Map a command to a callable handler
     *
     * @param string $commandName
     * @param CommandHandler|callable|string $handler
     * @return mixed
     */
    public function pushHandler($commandName, $handler);

    /**
     * Add a decorator
     *
     * @param CommandBus $decorator
     * @return mixed
     */
    public function addDecorator(CommandBus $decorator);
}