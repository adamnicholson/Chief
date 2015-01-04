<?php

namespace Chief;

interface CommandBus
{
    /**
     * Execute a command
     *
     * @param Command $command
     * @return mixed
     */
    public function execute(Command $command);

    /**
     * Map a command to a handler by name
     *
     * @param $commandName
     * @param $handlerName
     * @return mixed
     */
    public function mapHandler($commandName, $handlerName);

    /**
     * Map a command to a callable handler
     *
     * @param $commandName
     * @param callable $handler
     * @return mixed
     */
    public function pushHandler($commandName, callable $handler);
}