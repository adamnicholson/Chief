<?php

namespace Chief;

use Chief\Handlers\CallableCommandHandler;
use Chief\Handlers\StringCommandHandler;

class Chief implements CommandBus
{
    protected $handlers = [];

    public function __construct(CommandHandlerResolver $resolver = null)
    {
        $this->resolver = $resolver ?: new NativeCommandHandlerResolver;
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

        return $this->handle($command, $handler);
    }

    /**
     * Map a command to a CommandHandler
     *
     * @param $commandName
     * @param CommandHandler|callable|string $handler
     * @return mixed
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
            return $this->pushHandler($commandName, new StringCommandHandler($handler));
        }
    }

    /**
     * @param Command $command
     * @param CommandHandler|string $handler
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function handle(Command $command, $handler)
    {
        if ($handler instanceof CommandHandler) {
            return $handler->handle($command);
        }

        if (is_string($handler)) {
            $handler = $this->makeHandler($handler);
            return $this->handle($command, $handler);
        }

        throw new \InvalidArgumentException('Could not handle [' . get_class($command) . '] with handler [' . get_class($handler) . ']');
    }

    /**
     * Find a pushed handler
     *
     * @param Command $command
     * @return CommandHandler|string
     * @throws \InvalidArgumentException
     */
    protected function findHandler(Command $command)
    {
        foreach ($this->handlers as $handlerCommand => $handler) {
            if ($handlerCommand == get_class($command)) {
                return $handler;
            }
        }

        return $this->resolveHandler($command);
    }

    /**
     * Automatically detect the name of a CommandHandler from a Command
     *
     * @param Command|string $command
     * @return null|string
     */
    protected function resolveHandler($command)
    {
        $commandName = is_string($command) ? $command : get_class($command);
        return $this->resolver->resolveHandler($commandName);
    }

    /**
     * Make a CommandHandler class from its class name
     *
     * @param $handler
     * @return mixed
     */
    protected function makeHandler($handler)
    {
        return new $handler;
    }
}