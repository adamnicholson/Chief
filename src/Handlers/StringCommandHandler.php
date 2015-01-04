<?php

namespace Chief\Handlers;

use Chief\Command;
use Chief\CommandHandler;

class StringCommandHandler implements CommandHandler
{
    public function __construct($string)
    {
        $this->handler = new $string;
    }

    /**
     * Handle a command execution
     *
     * @param Command $command
     * @return mixed
     */
    public function handle(Command $command)
    {
        return $this->handler->handle($command);
    }

}