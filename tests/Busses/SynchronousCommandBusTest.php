<?php

namespace Chief\Busses;

use Chief\ChiefTestCase;
use Chief\CommandBus;
use Chief\Stubs\TestCommand;

class SynchronousCommandBusTest extends ChiefTestCase
{
    public function testInstance()
    {
        $this->assertTrue(new SynchronousCommandBus instanceof CommandBus);
    }

    public function testExecuteFiresHandlerProvidedByResolver()
    {
        $resolver = $this->getMock('Chief\CommandHandlerResolver');
        $handler = $this->getMock('Chief\CommandHandler');
        $bus = new SynchronousCommandBus($resolver);
        $command = new TestCommand;
        $handler->expects($this->once())->method('handle')->with($command);
        $resolver->expects($this->once())->method('resolve')->with($command)->willReturn($handler);
        $bus->execute($command);
    }
}