<?php

namespace Chief\Resolvers;

use Chief\ChiefTestCase;
use Chief\Command;
use Chief\CommandHandler;
use Chief\CommandHandlerResolver;
use Chief\Stubs\TestCommand;
use Chief\Stubs\TestCommandHandler;
use Chief\Stubs\TestCommandWithNestedHandler;
use Chief\Stubs\Handlers\TestCommandWithNestedHandlerHandler;
use Chief\Stubs\TestCommandWithoutHandler;

class NativeCommandHandlerResolverTest extends ChiefTestCase
{
    public function testInstantiable()
    {
        $this->assertTrue(new NativeCommandHandlerResolver instanceof CommandHandlerResolver);
    }

    public function testResolveThrowsExceptionWhenNoHandlerFound()
    {
        $resolver = new NativeCommandHandlerResolver;
        $this->setExpectedException(\Chief\Exceptions\UnresolvableCommandHandlerException::class);
        $resolver->resolve(new TestCommandWithoutHandler);
    }

    public function testResolveReturnsHandlerWhenNotBoundAndInSameNamespaceWithHandlerSuffix()
    {
        $resolver = new NativeCommandHandlerResolver;
        $handler = $resolver->resolve(new TestCommand);
        $this->assertTrue($handler instanceof TestCommandHandler);
    }

    public function testResolveReturnsHandlerWhenNotBoundAndHandlerNestedInHandlersNamespaceWithHandlerSuffix()
    {
        $resolver = new NativeCommandHandlerResolver;
        $handler = $resolver->resolve(new TestCommandWithNestedHandler);
        $this->assertTrue($handler instanceof TestCommandWithNestedHandlerHandler);
    }

    public function testResolveReturnsHandlerBoundByObject()
    {
        $handler = $this->getMockBuilder(\Chief\CommandHandler::class)->getMock();
        $resolver = new NativeCommandHandlerResolver;
        $resolver->bindHandler(\Chief\Stubs\TestCommandWithoutHandler::class, $handler);
        $this->assertEquals($resolver->resolve(new TestCommandWithoutHandler), $handler);
    }

    public function testResolveReturnsHandlerBoundByCallable()
    {
        $resolver = new NativeCommandHandlerResolver;
        $proof = new \stdClass();
        $resolver->bindHandler(\Chief\Stubs\TestCommandWithoutHandler::class, function (Command $command) use ($proof) {
                $proof->handled = true;
        });
        $command = new TestCommandWithoutHandler;
        $handler = $resolver->resolve($command);
        $this->assertTrue($handler instanceof CommandHandler);
        $handler->handle($command);
        $this->assertEquals($proof->handled, true);
    }

    public function testResolveReturnsHandlerBoundByString()
    {
        $resolver = new NativeCommandHandlerResolver;
        $resolver->bindHandler(\Chief\Stubs\TestCommandWithoutHandler::class, \Chief\Stubs\TestCommandHandler::class);
        $command = new TestCommand;
        $handler = $resolver->resolve($command);
        $this->assertTrue($handler instanceof CommandHandler);
        $handler->handle($command);
        $this->assertEquals($command->handled, true);
    }
}
