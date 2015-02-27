<?php

namespace Chief\Stubs;

use Chief\Command;
use Chief\CommandHandler;

class NonInterfaceImplementingCommandHandler
{
    public function handle($command)
    {
        $command->handled = true;
    }
}