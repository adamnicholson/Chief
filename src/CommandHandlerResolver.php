<?php

namespace Chief;

interface CommandHandlerResolver
{
    /**
     * Retrieve a CommandHandler for a given Command
     *
     * @param Command $command
     * @return CommandHandler
     */
    public function resolve(Command $command);

    /**
     * Bind a handler to a command. These bindings should overrule the default
     * resolution behaviour for this resolver
     *
     * @param string $commandName
     * @param CommandHandler|callable|string $handler
     */
    public function bindHandler($commandName, $handler);
}