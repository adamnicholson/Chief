<?php

namespace Chief\Decorator\Log;

use Chief\CommandBus;
use Chief\Decorator\Log\LoggingDecorator;
use Chief\Decorator\DecoratorTest;
use Chief\Stubs\TestCommand;

class LoggingDecoratorTest extends DecoratorTest
{
    public function testInstance()
    {
        $decorator = $this->getDecorator();
        $decorator->setInnerBus($this->getMockBuilder(\Chief\CommandBus::class)->getMock());
        $this->assertTrue($decorator instanceof CommandBus);
    }

    public function testExecuteLogsMessageAndFiresInnerBus()
    {
        $decorator = new LoggingDecorator(
            $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock()
        );
        $decorator->setInnerBus($bus = $this->getMockBuilder(\Chief\CommandBus::class)->getMock());
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
        $decorator->setInnerBus($bus = $this->getMockBuilder(\Chief\CommandBus::class)->getMock());
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
