<?php

namespace Chief\Handlers;

use Chief\Command;
use Chief\CommandHandler;

class CallableCommandHandler implements CommandHandler
{
    /**
     * @var callable
     */
    protected $handler;

    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Handle a command execution
     *
     * @param Command $command
     * @return mixed
     */
    public function handle(Command $command)
    {
        $callableHandler = $this->handler;
        return $callableHandler($command);
    }
}
