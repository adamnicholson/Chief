<?php

namespace Chief\Stubs\Handlers;

use Chief\Command;
use Chief\CommandHandler;

class TestCommandWithNestedHandlerHandler implements CommandHandler
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