<?php

namespace Chief;

class NativeCommandHandlerResolver implements CommandHandlerResolver
{
    /**
     * Automatically resolve a handler from a command
     *
     * @param Command $command
     * @return CommandHandler|callable|string
     */
    public function resolveHandler(Command $command)
    {
        $commandName = get_class($command);

        if (class_exists($commandName . 'Handler')) {
            return $commandName . 'Handler';
        }

        return null;
    }

}