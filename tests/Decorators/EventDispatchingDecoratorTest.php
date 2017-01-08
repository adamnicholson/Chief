<?php

namespace Chief\Decorators;

use Chief\CommandBus;
use Chief\Stubs\TestCommand;

class EventDispatchingDecoratorTest extends DecoratorTest
{
    public function testInstance()
    {
        $decorator = $this->getDecorator();
        $decorator->setInnerBus($this->getMockBuilder('Chief\CommandBus')->getMock());
        $this->assertTrue($decorator instanceof CommandBus);
    }

    public function testExecuteFiresEventAndInnerBus()
    {
        $decorator = new EventDispatchingDecorator(
            $dispatcher = $this->getMockBuilder('Chief\Decorators\EventDispatcher')->getMock()
        );
        $decorator->setInnerBus($bus = $this->getMockBuilder('Chief\CommandBus')->getMock());
        $command = new TestCommand();
        $bus->expects($this->once())->method('execute')->with($command);
        $dispatcher->expects($this->once())->method('dispatch')->with('Chief.Stubs.TestCommand', [$command]);
        $decorator->execute($command);
    }

    protected function getDecorator()
    {
        return new EventDispatchingDecorator($this->getMockBuilder('Chief\Decorators\EventDispatcher')->getMock());
    }
}