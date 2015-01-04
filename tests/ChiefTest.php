<?php

namespace Chief;

use Chief\Busses\SynchronousCommandBus;
use Chief\Resolvers\NativeCommandHandlerResolver;
use Chief\Stubs\LogDecoratorCommandBus;
use Chief\Stubs\SelfHandlingCommand;
use Chief\Stubs\TestCommand;
use Chief\Stubs\TestCommandWithoutHandler;

class ChiefTest extends ChiefTestCase
{
    public function testInstantiable()
    {
        $this->assertTrue(new Chief instanceof CommandBus);
    }

    public function testExecuteFiresByAutoResolution()
    {
        $bus = new Chief();
        $command = new TestCommand;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresHandlerAttachedByInstance()
    {
        $resolver = new NativeCommandHandlerResolver;
        $resolver->bindHandler('Chief\Stubs\TestCommand', $handler = $this->getMock('Chief\CommandHandler'));
        $syncBus = new SynchronousCommandBus($resolver);
        $bus = new Chief($syncBus);
        $command = new TestCommand;
        $handler->expects($this->once())->method('handle')->with($command);
        $bus->execute($command);
    }

    public function testExecuteFiresHandlerAttachedByCallable()
    {
        $resolver = new NativeCommandHandlerResolver;
        $resolver->bindHandler('Chief\Stubs\TestCommand', function (Command $command) {
                $command->handled = true;
        });
        $bus = new Chief(new SynchronousCommandBus($resolver));
        $command = new TestCommand;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresHandlerAttachedByString()
    {
        $resolver = new NativeCommandHandlerResolver;
        $resolver->bindHandler('Chief\Stubs\TestCommand', 'Chief\Stubs\TestCommandHandler');
        $bus = new Chief(new SynchronousCommandBus($resolver));
        $command = new TestCommand;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteThrowsExceptionWhenNoHandler()
    {
        $bus = new Chief();
        $command = new TestCommandWithoutHandler;
        $this->setExpectedException('Exception');
        $bus->execute($command);
    }

    public function testCommandCanHandleItselfIfImplementsCommandHandler()
    {
        $bus = new Chief();
        $command = new SelfHandlingCommand;
        $bus->execute($command);
    }

    public function testDecoratorCommandBus()
    {
        $bus = new LogDecoratorCommandBus(
            $logger = $this->getMock('Psr\Log\LoggerInterface'),
            $innerBus = $this->getMock('Chief\Busses\SynchronousCommandBus')
        );
        $chief = new Chief($bus);
        $command = new TestCommand;
        $logger->expects($this->exactly(2))->method('info');
        $innerBus->expects($this->once())->method('execute')->with($command);
        $chief->execute($command);
    }
}
