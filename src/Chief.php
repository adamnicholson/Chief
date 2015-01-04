<?php

namespace Chief;

class Chief implements CommandBus
{
    protected $handlers = [];

    /**
     * Execute a command
     *
     * @param Command $command
     * @return mixed
     */
    public function execute(Command $command)
    {
        $handler = $this->findHandler($command);

        if (is_callable($handler)) {
            $handler($command);
        }
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
     * Map a command to a callable handler
     *
     * @param $commandName
     * @param callable $handler
     * @return mixed
     */
    public function pushHandler($commandName, callable $handler)
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