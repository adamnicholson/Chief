<?php

namespace Chief;


use Chief\Exceptions\UnresolvableCommandHandlerException;

class NativeCommandHandlerResolver implements CommandHandlerResolver
{
    /**
     * Automatically resolve a handler from a command
     *
     * @param string $command
     * @return CommandHandler
     * @throws
     */
    public function resolve($command)
    {
        $class = $command . 'Handler';
        if (class_exists($class)) {
            return new $class;
        }

        throw new UnresolvableCommandHandlerException('Could not resolve a handler for ');
    }

}