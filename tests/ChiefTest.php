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
        $foo = new \stdClass();
        $foo->called = false;
        $handler = function (Command $command) use ($foo) {
            $foo->called = true;
        };
        $chief = new Chief();
        $chief->pushHandler('Chief\ChiefTestCommandStub', $handler);
        $chief->execute(new ChiefTestCommandStub);
        $this->assertEquals($foo->called, true);
    }
}

class ChiefTestCommandStub implements Command {}