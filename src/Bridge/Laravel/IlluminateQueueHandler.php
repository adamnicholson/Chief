<?php

namespace Chief\Bridge\Laravel;

use Chief\Busses\SynchronousCommandBus;

class IlluminateQueueHandler
{
    public function __construct(SynchronousCommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function fire($job, $serializedCommand)
    {
        $command = unserialize($serializedCommand);

        $this->bus->execute($command);
    }
}
