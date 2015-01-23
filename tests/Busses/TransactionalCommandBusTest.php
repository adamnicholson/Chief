<?php

namespace Chief\Busses;

use Chief\ChiefTestCase;
use Chief\CommandBus;
use Chief\Resolvers\NativeCommandHandlerResolver;
use Chief\Stubs\TestCommand;
use Chief\Stubs\TestTransactionalCommand;

class TransactionalCommandBusTest extends ChiefTestCase
{
    public function testInstance()
    {
        $this->assertTrue(new TransactionalCommandBus instanceof CommandBus);
    }

    public function testNestedCommandsExecutedAfterInitialCommand()
    {
        $syncBus = new SynchronousCommandBus($resolver = new NativeCommandHandlerResolver());
        $bus = new TransactionalCommandBus($syncBus);
        $command = new TestTransactionalCommand();

        $lastCalled = null;
        $resolver->bindHandler('Chief\Stubs\TestCommand', function () use ($bus, &$lastCalled) {
            $lastCalled = 'TestCommand';
        });

        $resolver->bindHandler('Chief\Stubs\TestTransactionalCommand', function () use ($bus, &$lastCalled) {
            $bus->execute(new TestCommand());
            $lastCalled = 'TestTransactionalCommand';
        });
        $bus->execute($command);

        $this->assertEquals($lastCalled, 'TestCommand');
    }

    public function testNestedCommandsRanWhenMultiple()
    {
        $syncBus = new SynchronousCommandBus($resolver = new NativeCommandHandlerResolver());
        $bus = new TransactionalCommandBus($syncBus);
        $command = new TestTransactionalCommand();

        $countTestCommandCalled = 0;
        $resolver->bindHandler('Chief\Stubs\TestCommand', function () use ($bus, &$countTestCommandCalled) {
            $countTestCommandCalled++;
        });

        $resolver->bindHandler('Chief\Stubs\TestTransactionalCommand', function () use ($bus, &$lastCalled) {
            $bus->execute(new TestCommand());
            $bus->execute(new TestCommand());
            $bus->execute(new TestCommand());
        });
        $bus->execute($command);

        $this->assertEquals($countTestCommandCalled, 3);
    }

    public function testNestedCommandsNotExecutedWhenInitialCommandFailsBeforeReturning()
    {
        $syncBus = new SynchronousCommandBus($resolver = new NativeCommandHandlerResolver());
        $bus = new TransactionalCommandBus($syncBus);
        $command = new TestTransactionalCommand();

        $countTestCommandCalled = 0;
        $resolver->bindHandler('Chief\Stubs\TestCommand', function () use ($bus, &$countTestCommandCalled) {
            $countTestCommandCalled++;
        });

        $resolver->bindHandler('Chief\Stubs\TestTransactionalCommand', function () use ($bus, &$lastCalled) {
            $bus->execute(new TestCommand());
            $bus->execute(new TestCommand());
            $bus->execute(new TestCommand());
            throw new \Exception('Something failed');
        });

        $this->setExpectedException('Exception');
        $bus->execute($command);

        $this->assertEquals($countTestCommandCalled, 0);
    }
}