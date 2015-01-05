<?php

namespace Chief;

interface CommandHandlerResolver
{
    /**
     * Automatically resolve a handler from a command
     *
     * @param Command $command
     * @return CommandHandler
     */
    public function resolve(Command $command);

    /**
     * Bind a handler to a command. These bindings should overrule the default
     * resolution behaviour for this resolver
     *
     * @param $commandName
     * @param $handler
     */
    public function bindHandler($commandName, $handler);
}