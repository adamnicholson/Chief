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
        $resolver = $this->createMock('Chief\CommandHandlerResolver');
        $handler = $this->createMock('Chief\CommandHandler');
        $bus = new SynchronousCommandBus($resolver);
        $command = new TestCommand;
        $handler->expects($this->once())->method('handle')->with($command);
        $resolver->expects($this->once())->method('resolve')->with($command)->willReturn($handler);
        $bus->execute($command);
    }

    public function testExecuteReturnsHandlerResponse()
    {
        $resolver = $this->createMock('Chief\CommandHandlerResolver');
        $handler = $this->createMock('Chief\CommandHandler');
        $bus = new SynchronousCommandBus($resolver);
        $command = new TestCommand;
        $handler->expects($this->once())->method('handle')->with($command)->willReturn('Foo-Bar.');
        $resolver->expects($this->once())->method('resolve')->with($command)->willReturn($handler);
        $response = $bus->execute($command);
        $this->assertEquals($response, 'Foo-Bar.');
    }
}