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
        return $this->findHandler($command)->handle($command);
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
     * Find a pushed handler
     *
     * @param Command $command
     * @return CommandHandler
     * @throws \InvalidArgumentException
     */
    protected function findHandler(Command $command)
    {
        foreach ($this->handlers as $handlerCommand => $handler) {
            if ($handlerCommand == get_class($command)) {
                return $handler;
            }
        }

        return $this->resolver->resolveHandler(get_class($command));
    }
}