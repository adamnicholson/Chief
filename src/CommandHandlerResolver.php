<?php

namespace Chief;

interface CommandHandlerResolver
{
    /**
     * Automatically resolve a handler from a command
     *
     * @param Command $command
     * @return string
     */
    public function resolveHandler($command);
}