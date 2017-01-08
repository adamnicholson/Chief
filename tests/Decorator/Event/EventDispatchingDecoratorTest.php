<?php

namespace Chief\Decorator\Event;

use Chief\CommandBus;
use Chief\Decorator\Event\EventDispatcher;
use Chief\Decorator\Event\EventDispatchingDecorator;
use Chief\Decorator\DecoratorTest;
use Chief\Stubs\TestCommand;

class EventDispatchingDecoratorTest extends DecoratorTest
{
    public function testInstance()
    {
        $decorator = $this->getDecorator();
        $decorator->setInnerBus($this->getMockBuilder(\Chief\CommandBus::class)->getMock());
        $this->assertTrue($decorator instanceof CommandBus);
    }

    public function testExecuteFiresEventAndInnerBus()
    {
        $decorator = new EventDispatchingDecorator(
            $dispatcher = $this->getMockBuilder(EventDispatcher::class)->getMock()
        );
        $decorator->setInnerBus($bus = $this->getMockBuilder(\Chief\CommandBus::class)->getMock());
        $command = new TestCommand();
        $bus->expects($this->once())->method('execute')->with($command);
        $dispatcher->expects($this->once())->method('dispatch')->with('Chief.Stubs.TestCommand', [$command]);
        $decorator->execute($command);
    }

    protected function getDecorator()
    {
        return new EventDispatchingDecorator($this->getMockBuilder(EventDispatcher::class)->getMock());
    }
}