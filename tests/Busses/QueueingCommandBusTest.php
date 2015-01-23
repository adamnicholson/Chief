<?php

namespace Chief\Busses;

use Chief\ChiefTestCase;
use Chief\CommandBus;
use Chief\Stubs\TestCommand;
use Chief\Stubs\TestQueueableCommand;

class QueueingCommandBusTest extends ChiefTestCase
{
    public function testInstance()
    {
        $this->assertTrue(new QueueingCommandBus($this->getMock('Chief\CommandBusQueuer')) instanceof CommandBus);
    }

    public function testExecutePutsNormalCommandInInnerBus()
    {
        $queuer = $this->getMock('Chief\CommandBusQueuer');
        $innerBus = $this->getMock('Chief\CommandBus');
        $bus = new QueueingCommandBus($queuer, $innerBus);
        $command = new TestCommand;
        $queuer->expects($this->never())->method('queue');
        $innerBus->expects($this->once())->method('execute')->with($command);
        $bus->execute($command);
    }

    public function testExecutePutsQueueableCommandInQueuer()
    {
        $queuer = $this->getMock('Chief\CommandBusQueuer');
        $bus = new QueueingCommandBus($queuer);
        $command = new TestQueueableCommand;
        $queuer->expects($this->once())->method('queue')->with($command);
        $bus->execute($command);
    }
}