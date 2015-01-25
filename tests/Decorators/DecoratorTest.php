<?php

namespace Chief\Decorators;

use Chief\ChiefTestCase;
use Chief\Stubs\TestCommand;

abstract class DecoratorTest extends ChiefTestCase
{
    public function testExecuteFiresInnerBusAndReturnsResponse()
    {
        $decorator = $this->getDecorator();
        $decorator->setInnerBus($bus = $this->getMock('Chief\CommandBus'));
        $command = new TestCommand();
        $bus->expects($this->once())->method('execute')->with($command)->willReturn('Some response');
        $response = $decorator->execute($command);
        $this->assertEquals($response, 'Some response');
    }

    /**
     * @return \Chief\Decorator
     */
    abstract protected function getDecorator();
}