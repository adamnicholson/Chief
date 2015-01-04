<?php

namespace Chief\Stubs;

use Chief\Command;
use Chief\CommandHandler;

class TestCommandHandler implements CommandHandler
{
    /**
     * Handle a command execution
     *
     * @param Command $command
     * @return mixed
     */
    public function handle(Command $command)
    {
        $command->handled = true;
    }
}