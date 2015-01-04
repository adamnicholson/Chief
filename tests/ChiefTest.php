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
        $chief = new Chief();
        $chief->pushHandler('Chief\ChiefTestCommandStub', new ChiefTestCommandStubHandler);
        $command = new ChiefTestCommandStub;
        $chief->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresHandlerAttachedByCallable()
    {
        $chief = new Chief();
        $chief->pushHandler('Chief\ChiefTestCommandStub', function (Command $command) {
            $command->handled = true;
        });
        $command = new ChiefTestCommandStub;
        $chief->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresHandlerAttachedByString()
    {
        $chief = new Chief();
        $chief->pushHandler('Chief\ChiefTestCommandStub', 'Chief\ChiefTestCommandStubHandler');
        $command = new ChiefTestCommandStub;
        $chief->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresByAutoResolution()
    {
        $chief = new Chief();
        $command = new ChiefTestCommandStub;
        $chief->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteThrowsExceptionWhenNoHandler()
    {
        $chief = new Chief();
        $command = new ChiefTestCommandWithoutHandlerStub;
        $this->setExpectedException('Exception');
        $chief->execute($command);
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