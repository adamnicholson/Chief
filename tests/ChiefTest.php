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
        $chief->pushHandler('Chief\ChiefTestCommandStub', new ChiefTestCommandHandlerStub);
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
        // @todo
    }
}

class ChiefTestCommandStub implements Command {}
class ChiefTestCommandHandlerStub implements CommandHandler
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