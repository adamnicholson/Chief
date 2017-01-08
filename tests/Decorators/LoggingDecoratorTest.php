<?php

namespace Chief\Decorators;

use Chief\CommandBus;
use Chief\Stubs\TestCommand;

class LoggingDecoratorTest extends DecoratorTest
{
    public function testInstance()
    {
        $decorator = $this->getDecorator();
        $decorator->setInnerBus($this->getMockBuilder('Chief\CommandBus')->getMock());
        $this->assertTrue($decorator instanceof CommandBus);
    }

    public function testExecuteLogsMessageAndFiresInnerBus()
    {
        $decorator = new LoggingDecorator(
            $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock()
        );
        $decorator->setInnerBus($bus = $this->getMockBuilder('Chief\CommandBus')->getMock());
        $command = new TestCommand();
        $bus->expects($this->once())->method('execute')->with($command);
        $logger->expects($this->exactly(2))->method('debug')->with($this->anything(), ['Command' => serialize($command), 'Context' => null]);
        $decorator->execute($command);
    }

    public function testExecuteLogsExecptionIfThrownByInnerBusAndBubbleException()
    {
        $decorator = new LoggingDecorator(
            $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock()
        );
        $decorator->setInnerBus($bus = $this->getMockBuilder('Chief\CommandBus')->getMock());
        $command = new TestCommand();
        $bus->expects($this->once())->method('execute')->with($command)->will($this->throwException(new \Exception('Oops')));
        $logger->expects($this->exactly(2))->method('debug')->with($this->anything(), ['Command' => serialize($command), 'Context' => null]);

        $this->setExpectedException('Exception');
        $decorator->execute($command);
    }

    /**
     * @return \Chief\Decorator
     */
    protected function getDecorator()
    {
        return new LoggingDecorator($this->getMockBuilder('Psr\Log\LoggerInterface')->getMock());
    }

}
