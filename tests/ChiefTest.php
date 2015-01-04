<?php

namespace Chief;

class ChiefTest extends ChiefTestCase
{
    public function testInstantiable()
    {
        $this->assertTrue(new Chief instanceof CommandBus);
    }

    public function testExecuteFiresHandlerAttachedByInstance()
    {
        $bus = new Chief();
        $bus->pushHandler('Chief\ChiefTestCommandStub', new ChiefTestCommandStubHandler);
        $command = new ChiefTestCommandStub;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresHandlerAttachedByCallable()
    {
        $bus = new Chief();
        $bus->pushHandler('Chief\ChiefTestCommandStub', function (Command $command) {
            $command->handled = true;
        });
        $command = new ChiefTestCommandStub;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresHandlerAttachedByString()
    {
        $bus = new Chief();
        $bus->pushHandler('Chief\ChiefTestCommandStub', 'Chief\ChiefTestCommandStubHandler');
        $command = new ChiefTestCommandStub;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresByAutoResolution()
    {
        $bus = new Chief();
        $command = new ChiefTestCommandStub;
        $bus->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteThrowsExceptionWhenNoHandler()
    {
        $bus = new Chief();
        $command = new ChiefTestCommandWithoutHandlerStub;
        $this->setExpectedException('Exception');
        $bus->execute($command);
    }
}

class ChiefTestCommandStub implements Command {}
class ChiefTestCommandWithoutHandlerStub implements Command {}
class ChiefTestCommandStubHandler implements CommandHandler
{
    /**
     * Handle a command execution
     *
     * @param Command $command
     * @return mixed
     */
    public function handle(Command $command)
    {
        $command->handled = true;
    }
}