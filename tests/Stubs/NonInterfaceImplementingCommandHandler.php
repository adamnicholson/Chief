<?php

namespace Chief\Stubs;

use Chief\Command;
use Chief\CommandHandler;

class NonInterfaceImplementingCommandHandler
{
    public $handled = false;

    public function handle($command)
    {
        $command->handled = true;
    }
}