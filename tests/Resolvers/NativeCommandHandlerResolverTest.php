<?php

namespace Chief\Resolvers;

use Chief\ChiefTestCase;
use Chief\CommandHandlerResolver;
use Chief\Stubs\TestCommand;
use Chief\Stubs\TestCommandHandler;
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
        $this->setExpectedException('Chief\Exceptions\UnresolvableCommandHandlerException');
        $resolver->resolve(new TestCommandWithoutHandler);
    }

    public function testResolveReturnsHandlerWhenWithHandlerSuffix()
    {
        $resolver = new NativeCommandHandlerResolver;
        $handler = $resolver->resolve(new TestCommand);
        $this->assertTrue($handler instanceof TestCommandHandler);
    }
}
