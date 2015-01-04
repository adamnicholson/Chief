<?php

namespace Chief;

interface CommandHandlerResolver
{
    /**
     * Automatically resolve a handler from a command
     *
     * @param string $command
     * @return CommandHandler
     */
    public function resolve($command);
}