<?php

namespace Chief\Busses;

use Chief\ChiefTestCase;
use Chief\CommandBus;
use Chief\Stubs\TestCommand;

class QueueingCommandBusTest extends ChiefTestCase
{
    public function testInstance()
    {
        $this->assertTrue(new QueueingCommandBus($this->getMock('Chief\CommandBusQueuer')) instanceof CommandBus);
    }

    public function testExecutePutsCommandInQueue()
    {
        $queuer = $this->getMock('Chief\CommandBusQueuer');
        $bus = new QueueingCommandBus($queuer);
        $command = new TestCommand;
        $queuer->expects($this->once())->method('queue')->with($command);
        $bus->execute($command);
    }
}