<?php

namespace Chief;

use Chief\SynchronousCommandBus;
use Chief\Decorator\Log\LoggingDecorator;
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
        $resolver->bindHandler(\Chief\Stubs\TestCommand::class, $handler = $this->getMockBuilder(\Chief\CommandHandler::class)->getMock());
        $syncBus = new SynchronousCommandBus($resolver);
        $bus = new Chief($syncBus);
        $command = new TestCommand;
        $handler->expects($this->once())->method('handle')->with($command);
        $bus->execute($command);
    }

    public function testExecuteFiresHandlerAttachedByCallable()
    {
        $resolver = new NativeCommandHandlerResolver;
        $resolver->bindHandler(\Chief\Stubs\TestCommand::class, function (Command $command) {
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
        $resolver->bindHandler(\Chief\Stubs\TestCommand::class, \Chief\Stubs\TestCommandHandler::class);
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
        $command = $this->getMockBuilder(\Chief\Stubs\SelfHandlingCommand::class)->getMock();
        $command->expects($this->once())->method('handle')->with($command);
        $bus->execute($command);
    }

    public function testDecoratorCommandBus()
    {
        $bus = new LogDecoratorCommandBus(
            $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock(),
            $innerBus = $this->getMockBuilder(\Chief\SynchronousCommandBus::class)->getMock()
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
            $decorator = $this->getMockBuilder(\Chief\Decorator::class)->getMock()
        ]);
        $command = new TestCommand;
        $decorator->expects($this->once())->method('execute')->with($command);
        $chief->execute($command);
    }

    public function testInstanceWithMultipleDecoratorsHitsNestedDecorators()
    {
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $chief = new Chief(new SynchronousCommandBus, [
            $decoratorOne = new LoggingDecorator($logger),
            $decoratorTwo = $this->getMockBuilder(\Chief\Decorator::class)->getMock(),
        ]);
        $command = new TestCommand;
        $decoratorTwo->expects($this->once())->method('execute')->with($command);
        $chief->execute($command);
    }

    public function testInstanceWithMultipleDecoratorsHitsHandler()
    {
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

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
        $chief = new Chief($bus = $this->getMockBuilder(\Chief\CommandBus::class)->getMock());
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
