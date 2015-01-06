<?php

namespace Chief\Decorators;

use Chief\ChiefTest;
use Chief\CommandBus;
use Chief\Stubs\TestCommand;

class LoggingDecoratorTest extends ChiefTest
{
    public function testInstance()
    {
        $decorator = new LoggingDecorator(
            $this->getMock('Psr\Log\LoggerInterface')
        );
        $decorator->setInnerBus($this->getMock('Chief\CommandBus'));
        $this->assertTrue($decorator instanceof CommandBus);
    }

    public function testExecuteFiresEventAndInnerBus()
    {
        $decorator = new LoggingDecorator(
            $logger = $this->getMock('Psr\Log\LoggerInterface')
        );
        $decorator->setInnerBus($bus = $this->getMock('Chief\CommandBus'));
        $command = new TestCommand();
        $bus->expects($this->once())->method('execute')->with($command);
        $logger->expects($this->exactly(2))->method('info')->with($this->anything(), [$command]);
        $decorator->execute($command);
    }
}