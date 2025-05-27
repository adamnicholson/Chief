<?php

namespace Chief\Resolvers;

use Chief\Command;
use Chief\CommandHandler;
use Chief\CommandHandlerResolver;
use Chief\Container;
use Chief\Exceptions\UnresolvableCommandHandlerException;
use Chief\Handlers\CallableCommandHandler;
use Chief\Handlers\LazyLoadingCommandHandler;
use Chief\Containers\NativeContainer;
use Psr\Container\ContainerInterface;

class NativeCommandHandlerResolver implements CommandHandlerResolver
{
    protected $container;

    protected $handlers = [];

    public function __construct(ContainerInterface|Container|null $container = null)
    {
        if ($container instanceof ContainerInterface) {
            $this->container = $container;
        } elseif ($container instanceof Container) {
            $this->container = $container;
        } elseif ($container === null) {
            $this->container = new NativeContainer;
        } else {
            throw new \InvalidArgumentException('Container must implement Psr\Container\ContainerInterface or Chief\Container');
        }
    }

    /**
     * Retrieve a CommandHandler for a given Command
     *
     * @param Command $command
     * @return CommandHandler
     * @throws UnresolvableCommandHandlerException
     */
    public function resolve(Command $command)
    {
        $commandName = get_class($command);

        // Find the CommandHandler if it has been manually defined using pushHandler()
        foreach ($this->handlers as $handlerCommand => $handler) {
            if ($handlerCommand == $commandName) {
                return $handler;
            }
        }

        // If the Command also implements CommandHandler, then it can handle() itself
        if ($command instanceof CommandHandler) {
            return $command;
        }

        // Try and guess the handler's name in the same namespace with suffix "Handler"
        $class = $commandName . 'Handler';
        if (class_exists($class)) {
            return $this->container->get($class);
        }

        // Try and guess the handler's name in nested "Handlers" namespace with suffix "Handler"
        $classParts = explode('\\', $commandName);
        $commandNameWithoutNamespace = array_pop($classParts);
        $class = implode('\\', $classParts) . '\\Handlers\\' . $commandNameWithoutNamespace . 'Handler';
        if (class_exists($class)) {
            return $this->container->get($class);
        }

        throw new UnresolvableCommandHandlerException('Could not resolve a handler for [' . get_class($command) . ']');
    }

    /**
     * Bind a handler to a command. These bindings should overrule the default
     * resolution behaviour for this resolver
     *
     * @param string $commandName
     * @param CommandHandler|callable|string $handler
     * @return void
     * @throws \InvalidArgumentException
     */
    public function bindHandler($commandName, $handler)
    {
        // If the $handler given is an instance of CommandHandler, simply bind that
        if ($handler instanceof CommandHandler) {
            $this->handlers[$commandName] = $handler;
            return;
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
