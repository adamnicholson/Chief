<?php

namespace Chief\Decorators;

use Chief\CommandBus;
use Chief\Stubs\TestCommand;

class EventDispatchingDecoratorTest extends DecoratorTest
{
    public function testInstance()
    {
        $decorator = $this->getDecorator();
        $decorator->setInnerBus($this->createMock('Chief\CommandBus'));
        $this->assertTrue($decorator instanceof CommandBus);
    }

    public function testExecuteFiresEventAndInnerBus()
    {
        $decorator = new EventDispatchingDecorator(
            $dispatcher = $this->createMock('Chief\Decorators\EventDispatcher')
        );
        $decorator->setInnerBus($bus = $this->createMock('Chief\CommandBus'));
        $command = new TestCommand();
        $bus->expects($this->once())->method('execute')->with($command);
        $dispatcher->expects($this->once())->method('dispatch')->with('Chief.Stubs.TestCommand', [$command]);
        $decorator->execute($command);
    }

    protected function getDecorator()
    {
        return new EventDispatchingDecorator($this->createMock('Chief\Decorators\EventDispatcher'));
    }
}