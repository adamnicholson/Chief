<?php

namespace Chief\Decorators;

use Chief\ChiefTestCase;
use Chief\CommandBus;
use Chief\Stubs\TestCommand;
use Chief\Stubs\TestQueueableCommand;

class CommandQueueingDecoratorTest extends DecoratorTest
{
    public function testInstance()
    {
        $this->assertTrue(new CommandQueueingDecorator($this->getMockBuilder('Chief\CommandQueuer')->getMock()) instanceof CommandBus);
    }

    public function testExecutePutsNormalCommandInInnerBus()
    {
        $queuer = $this->getMockBuilder('Chief\CommandQueuer')->getMock();
        $innerBus = $this->getMockBuilder('Chief\CommandBus')->getMock();
        $bus = new CommandQueueingDecorator($queuer, $innerBus);
        $command = new TestCommand;
        $queuer->expects($this->never())->method('queue');
        $innerBus->expects($this->once())->method('execute')->with($command);
        $bus->execute($command);
    }

    public function testExecutePutsQueueableCommandInQueuer()
    {
        $queuer = $this->getMockBuilder('Chief\CommandQueuer')->getMock();
        $bus = new CommandQueueingDecorator($queuer);
        $command = new TestQueueableCommand;
        $queuer->expects($this->once())->method('queue')->with($command);
        $bus->execute($command);
    }

    /**
     * @return \Chief\Decorator
     */
    protected function getDecorator()
    {
        return new CommandQueueingDecorator($this->getMockBuilder('Chief\CommandQueuer')->getMock());
    }


}
