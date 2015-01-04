<?php

namespace Chief;

class Chief implements CommandBus
{
    protected $handlers = [];

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
        $this->handlers[$commandName] = $handler;
    }

    /**
     * Find a pushed handler
     *
     * @param Command $command
     * @return CommandHandler|callable|string
     * @throws \InvalidArgumentException
     */
    protected function findHandler(Command $command)
    {
        foreach ($this->handlers as $handlerCommand => $handler) {
            if ($handlerCommand == get_class($command)) {
                return $handler;
            }
        }

        if (!is_null($handler = $this->resolveHandler($command))) {
            return $handler;
        }

        throw new \InvalidArgumentException('Could not find handler for command [' . get_class($command) . ']');
    }

    /**
     * @param Command $command
     * @param CommandHandler|callable|string $handler
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function handle(Command $command, $handler)
    {
        if ($handler instanceof CommandHandler) {
            return $handler->handle($command);
        }

        if (is_callable($handler)) {
            return $handler($command);
        }

        if (is_string($handler)) {
            $handler = $this->makeHandler($handler);
            return $this->handle($command, $handler);
        }

        throw new \InvalidArgumentException('Could not handle [' . get_class($command) . '] with handler [' . get_class($handler) . ']');
    }

    /**
     * Automatically detect the name of a CommandHandler from a Command
     *
     * @param Command $command
     * @return null|string
     */
    protected function resolveHandler(Command $command)
    {
        $commandName = get_class($command);

        if (class_exists($commandName . 'Handler')) {
            return $commandName . 'Handler';
        }

        return null;
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