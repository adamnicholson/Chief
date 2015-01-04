<?php

namespace Chief;

use Chief\Handlers\CallableCommandHandler;
use Chief\Handlers\StringCommandHandler;

class Chief implements CommandBus
{
    protected $handlers = [];

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var CommandHandlerResolver
     */
    protected $resolver;

    public function __construct(CommandHandlerResolver $resolver = null, Container $container = null)
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
        return $this->findHandler($command)->handle($command);
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
     * Find a CommandHandler for a given Command
     *
     * @param Command $command
     * @return CommandHandler
     */
    protected function findHandler(Command $command)
    {
        foreach ($this->handlers as $handlerCommand => $handler) {
            if ($handlerCommand == get_class($command)) {
                return $handler;
            }
        }

        return $this->resolver->resolve(get_class($command));
    }
}