<?php

namespace Chief\Busses;

use Chief\Command;
use Chief\CommandBus;
use Chief\CommandBusQueuer;

class QueueingCommandBus implements CommandBus
{
    protected $resolver;

    public function __construct(CommandBusQueuer $queuer)
    {
        $this->queuer = $queuer;
    }

    /**
     * Execute a command
     *
     * @param Command $command
     * @return mixed
     */
    public function execute(Command $command)
    {
        $this->queuer->queue($command);
    }
}
