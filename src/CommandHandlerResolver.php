<?php

namespace Chief;

interface CommandHandlerResolver
{
    /**
     * Automatically resolve a handler from a command
     *
     * @param Command $command
     * @return CommandHandler|callable|string
     */
    public function resolveHandler(Command $command);
}