<?php

namespace Chief;

use Chief\Stubs\SelfHandlingCommand;
use Chief\Stubs\TestCommand;
use Chief\Stubs\TestCommandHandler;
use Chief\Stubs\TestCommandWithoutHandler;

class ChiefTest extends ChiefTestCase
{
    public function testInstantiable()
    {
        $this->assertTrue(new Chief instanceof CommandBus);
    }

    public function testExecuteFiresHandlerAttachedByInstance()
    {
        $bus = new Chief();
        $bus->pushHandler('Chief\Stubs\TestCommand', new TestCommandHandler);
        $command = new TestCommand;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresHandlerAttachedByCallable()
    {
        $bus = new Chief();
        $bus->pushHandler('Chief\Stubs\TestCommand', function (Command $command) {
            $command->handled = true;
        });
        $command = new TestCommand;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresHandlerAttachedByString()
    {
        $bus = new Chief();
        $bus->pushHandler('Chief\Stubs\TestCommand', 'Chief\Stubs\TestCommandHandler');
        $command = new TestCommand;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresByAutoResolution()
    {
        $bus = new Chief();
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

    public function testPushHandlerThrowsExceptionWhenObjectPassedNotACommandHandler()
    {
        $bus = new Chief();
        $this->setExpectedException('InvalidArgumentException');
        $bus->pushHandler('Chief\Stubs\TestCommand', new \stdClass);
    }

    public function testCommandCanHandleItselfIfImplementsCommandHandler()
    {
        $bus = new Chief();
        $command = new SelfHandlingCommand;
        $bus->execute($command);
    }
}
