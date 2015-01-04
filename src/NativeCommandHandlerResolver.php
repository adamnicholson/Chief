<?php

namespace Chief;

class NativeCommandHandlerResolver implements CommandHandlerResolver
{
    /**
     * Automatically resolve a handler from a command
     *
     * @param Command $command
     * @return CommandHandler
     * @throws
     */
    public function resolveHandler($command)
    {
        $class = $command . 'Handler';
        if (class_exists($class)) {
            return new $class;
        }

        throw new \Exception('Could not resolve a handler for ');
    }

}