<?php

namespace Chief\Bridge\Laravel;

use Chief\Command;
use Chief\Decorator\Queue\CommandQueuer;

class IlluminateQueuer implements CommandQueuer
{
    /**
     * Queue a Command for executing
     *
     * @param Command $command
     * @return mixed
     */
    public function queue(Command $command)
    {
        \Queue::push('Chief\Bridge\Laravel\IlluminateQueueHandler@fire', serialize($command));
    }
}
