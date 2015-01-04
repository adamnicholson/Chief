<?php

namespace Chief;


use Chief\Exceptions\UnresolvableCommandHandlerException;

class NativeCommandHandlerResolver implements CommandHandlerResolver
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

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
            return $this->container->make($class);
        }

        throw new UnresolvableCommandHandlerException('Could not resolve a handler for ');
    }

}