<?php

namespace Chief\Decorator\Queue;

use Chief\Command;

interface CommandQueuer
{
    /**
     * Queue a Command for executing
     *
     * @param Command $command
     */
    public function queue(Command $command);
}
