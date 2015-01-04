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
     * @return void
     */
    public function execute(Command $command)
    {
        $handler = $this->findHandler($command);

        if ($handler instanceof CommandHandler) {
            $handler->handle($command);
            return;
        }

        throw new \InvalidArgumentException('Could not find a handler for [' . get_class($command) . ']');
    }

    /**
     * Map a command to a handler by name
     *
     * @param $commandName
     * @param $handlerName
     * @return mixed
     */
    public function mapHandler($commandName, $handlerName)
    {
        // TODO: Implement mapHandler() method.
    }

    /**
     * Map a command to a CommandHandler
     *
     * @param $commandName
     * @param CommandHandler $handler
     * @return mixed
     */
    public function pushHandler($commandName, CommandHandler $handler)
    {
        $this->handlers[$commandName] = $handler;
    }

    protected function findHandler(Command $command)
    {
        foreach ($this->handlers as $handlerCommand => $handler) {
            if ($handlerCommand == get_class($command)) {
                return $handler;
            }
        }

        return null;
    }

}