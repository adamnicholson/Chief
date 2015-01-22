<?php

namespace Chief\Decorators;

use Chief\ChiefTestCase;
use Chief\CommandBus;
use Chief\Stubs\TestCommand;

class LoggingDecoratorTest extends ChiefTestCase
{
    public function testInstance()
    {
        $decorator = new LoggingDecorator(
            $this->getMock('Psr\Log\LoggerInterface')
        );
        $decorator->setInnerBus($this->getMock('Chief\CommandBus'));
        $this->assertTrue($decorator instanceof CommandBus);
    }

    public function testExecuteLogsMessageAndFiresInnerBus()
    {
        $decorator = new LoggingDecorator(
            $logger = $this->getMock('Psr\Log\LoggerInterface')
        );
        $decorator->setInnerBus($bus = $this->getMock('Chief\CommandBus'));
        $command = new TestCommand();
        $bus->expects($this->once())->method('execute')->with($command);
        $logger->expects($this->exactly(2))->method('debug')->with($this->anything(), [$command]);
        $decorator->execute($command);
    }

    public function testExecuteLogsExecptionIfThrownByInnerBusAndBubbleException()
    {
        $decorator = new LoggingDecorator(
            $logger = $this->getMock('Psr\Log\LoggerInterface')
        );
        $decorator->setInnerBus($bus = $this->getMock('Chief\CommandBus'));
        $command = new TestCommand();
        $bus->expects($this->once())->method('execute')->with($command)->will($this->throwException(new \Exception('Oops')));
        $logger->expects($this->exactly(2))->method('debug')->with($this->anything(), [$command]);

        $this->setExpectedException('Exception');
        $decorator->execute($command);
    }
}