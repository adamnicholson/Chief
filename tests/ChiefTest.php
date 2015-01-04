<?php

namespace Chief;

class ChiefTest extends ChiefTestCase
{
    public function testInstantiable()
    {
        $this->assertTrue(new Chief instanceof CommandBus);
    }

    public function testExecuteFiresHandlerAttachedByPushHandler()
    {
        $chief = new Chief();
        $chief->pushHandler('Chief\ChiefTestCommandStub', new ChiefTestCommandHandlerStub);
        $command = new ChiefTestCommandStub;
        $chief->execute($command);
        $this->assertEquals($command->handled, true);
    }

    public function testExecuteFiresHandlerAttachedByMapHandler()
    {

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