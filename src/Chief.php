<?php

namespace Chief;

use Chief\Handlers\CallableCommandHandler;
use Chief\Handlers\StringCommandHandler;

class Chief implements CommandBus
{
    protected $handlers = array();
    protected $decorators = array();

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var CommandHandlerResolver
     */
    protected $resolver;

    public function __construct(Container $container = null, CommandHandlerResolver $resolver = null)
    {
        $this->container = $container ?: new NativeContainer;
        $this->resolver = $resolver ?: new NativeCommandHandlerResolver($this->container);
    }

    /**
     * Execute a command
     *
     * @param Command $command
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function execute(Command $command)
    {
        $handler = $this->findHandler($command);

        $this->executeDecorators($command);

        return $handler->handle($command);
    }

    /**
     * Map a command to a CommandHandler
     *
     * @param string $commandName
     * @param CommandHandler|callable|string $handler
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function pushHandler($commandName, $handler)
    {
        if ($handler instanceof CommandHandler) {
            $this->handlers[$commandName] = $handler;
            return true;
        }

        if (is_callable($handler)) {
            return $this->pushHandler($commandName, new CallableCommandHandler($handler));
        }

        if (is_string($handler)) {
            return $this->pushHandler($commandName, new StringCommandHandler($handler, $this->container));
        }

        throw new \InvalidArgumentException('Could not push handler. Command Handlers should be an
            instance of Chief\CommandHandler');
    }

    /**
     * Add a CommandBus decorator
     *
     * @param CommandBus $decorator
     * @return void
     */
    public function addDecorator(CommandBus $decorator)
    {
        $this->decorators[] = $decorator;
    }

    /**
     * Find a CommandHandler for a given Command
     *
     * @param Command $command
     * @return CommandHandler
     */
    protected function findHandler(Command $command)
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

        // If we got here then we couldn't find the command, so ask the Resolver to find it
        return $this->resolver->resolve(get_class($command));
    }

    /**
     * Execute all the CommandBus decorators for a Command
     *
     * @param Command $command
     * @return void
     */
    protected function executeDecorators(Command $command)
    {
        foreach ($this->decorators as $decorator) {
            $decorator->execute($command);
        }
    }
}