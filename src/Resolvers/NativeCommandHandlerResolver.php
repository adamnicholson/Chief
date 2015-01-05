<?php

namespace Chief\Resolvers;

use Chief\Command;
use Chief\CommandHandler;
use Chief\CommandHandlerResolver;
use Chief\Container;
use Chief\Exceptions\UnresolvableCommandHandlerException;
use Chief\Handlers\CallableCommandHandler;
use Chief\Handlers\LazyLoadingCommandHandler;
use Chief\Handlers\StringCommandHandler;
use Chief\NativeContainer;

class NativeCommandHandlerResolver implements CommandHandlerResolver
{
    protected $container;

    protected $handlers = [];

    public function __construct(Container $container = null)
    {
        $this->container = $container ?: new NativeContainer;
    }

    /**
     * Automatically resolve a handler from a command
     *
     * @param Command $command
     * @return CommandHandler
     * @throws
     */
    public function resolve(Command $command)
    {
        // Find the CommandHandler if it has been manually defined using pushHandler()
        foreach ($this->handlers as $handlerCommand => $handler) {
            if ($handlerCommand == get_class($command)) {
                return $handler;
            }
        }

        // If the Command also implements CommandHandler, then it can handle() itself
        if ($command instanceof CommandHandler) {
            return $command;
        }

        // Try and guess the handler's name
        $class = get_class($command) . 'Handler';
        if (class_exists($class)) {
            return $this->container->make($class);
        }

        throw new UnresolvableCommandHandlerException('Could not resolve a handler for [' . get_class($command));
    }

    public function bindHandler($commandName, $handler)
    {
        // If the $handler given is an instance of CommandHandler, simply bind that
        if ($handler instanceof CommandHandler) {
            $this->handlers[$commandName] = $handler;
            return true;
        }

        // If the handler given is callable, wrap it up in a CallableCommandHandler for executing later
        if (is_callable($handler)) {
            return $this->bindHandler($commandName, new CallableCommandHandler($handler));
        }

        // If the handler given is a string, wrap it up in a LazyLoadingCommandHandler for loading later
        if (is_string($handler)) {
            return $this->bindHandler($commandName, new LazyLoadingCommandHandler($handler, $this->container));
        }

        throw new \InvalidArgumentException('Could not push handler. Command Handlers should be an
            instance of Chief\CommandHandler, a callable, or a string representing a CommandHandler class');
    }
}
