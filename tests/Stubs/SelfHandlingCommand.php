<?php

namespace Chief\Stubs;

use Chief\Command;
use Chief\CommandHandler;

class SelfHandlingCommand implements Command, CommandHandler
{
    public function handle(Command $command)
    {
        $command->handled = true;
    }
}