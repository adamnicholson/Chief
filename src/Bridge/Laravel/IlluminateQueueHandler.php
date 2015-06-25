<?php

namespace Chief\Bridge\Laravel;

use Chief\CommandBus;

class IlluminateQueueHandler
{
    protected $bus;

    /**
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @param \Illuminate\Contracts\Queue\Job $job
     * @param $serializedCommand
     */
    public function fire($job, $serializedCommand)
    {
        $command = unserialize($serializedCommand);

        $this->bus->execute($command);

        $job->delete();
    }
}
