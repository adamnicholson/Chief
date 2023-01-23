<?php

namespace Chief;

use Chief\Busses\SynchronousCommandBus;
use Chief\Decorators\LoggingDecorator;
use Chief\Resolvers\NativeCommandHandlerResolver;
use Chief\Stubs\LogDecoratorCommandBus;
use Chief\Stubs\NonInterfaceImplementingCommand;
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
        $resolver->bindHandler('Chief\Stubs\TestCommand', $handler = $this->createMock('Chief\CommandHandler'));
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
        $this->expectException('Exception');
        $bus->execute($command);
    }

    public function testCommandCanHandleItselfIfImplementsCommandHandler()
    {
        $bus = new Chief();
        $command = $this->createMock('Chief\Stubs\SelfHandlingCommand');
        $command->expects($this->once())->method('handle')->with($command);
        $bus->execute($command);
    }

    public function testDecoratorCommandBus()
    {
        $bus = new LogDecoratorCommandBus(
            $logger = $this->createMock('Psr\Log\LoggerInterface'),
            $innerBus = $this->createMock('Chief\Busses\SynchronousCommandBus')
        );
        $chief = new Chief($bus);
        $command = new TestCommand;
        $logger->expects($this->exactly(2))->method('info');
        $innerBus->expects($this->once())->method('execute')->with($command);
        $chief->execute($command);
    }

    public function testInstanceWithDecorators()
    {
        $chief = new Chief(new SynchronousCommandBus, [
            $decorator = $this->createMock('Chief\Decorator')
        ]);
        $command = new TestCommand;
        $decorator->expects($this->once())->method('execute')->with($command);
        $chief->execute($command);
    }

    public function testInstanceWithMultipleDecoratorsHitsNestedDecorators()
    {
        $logger = $this->createMock('Psr\Log\LoggerInterface');

        $chief = new Chief(new SynchronousCommandBus, [
            $decoratorOne = new LoggingDecorator($logger),
            $decoratorTwo = $this->createMock('Chief\Decorator'),
        ]);
        $command = new TestCommand;
        $decoratorTwo->expects($this->once())->method('execute')->with($command);
        $chief->execute($command);
    }

    public function testInstanceWithMultipleDecoratorsHitsHandler()
    {
        $logger = $this->createMock('Psr\Log\LoggerInterface');

        $chief = new Chief(new SynchronousCommandBus, [
            $decoratorOne = new LoggingDecorator($logger),
            $decoratorTwo = new LoggingDecorator($logger),
        ]);
        $command = new SelfHandlingCommand;
        $chief->execute($command);
        $this->assertEquals($command->handled, true);
    }
    public function testInnerBusResponseIsReturnedByChief()
    {
        $chief = new Chief($bus = $this->createMock('Chief\CommandBus'));
        $bus->expects($this->once())->method('execute')->willReturn('foo-bar');
        $response = $chief->execute(new TestCommand);
        $this->assertEquals($response, 'foo-bar');
    }

    public function testExecuteWithHandlerWhichDoesNotImplementInterface()
    {
        $command = new NonInterfaceImplementingCommand();
        $chief = new Chief();
        $chief->execute($command);
        $this->assertTrue($command->handled);
    }
}
