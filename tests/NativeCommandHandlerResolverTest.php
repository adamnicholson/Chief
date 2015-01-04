<?php

namespace Chief;

use Chief\Stubs\TestCommandHandler;

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
        $resolver->resolve('Chief\Stubs\TestCommandWithoutHandler');
    }

    public function testResolveReturnsHandlerWhenWithHandlerSuffix()
    {
        $resolver = new NativeCommandHandlerResolver;
        $handler = $resolver->resolve('Chief\Stubs\TestCommand');
        $this->assertTrue($handler instanceof TestCommandHandler);
    }
}
