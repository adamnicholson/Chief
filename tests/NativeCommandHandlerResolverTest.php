<?php

namespace Chief;

use Chief\Stubs\TestCommandHandler;

class NativeCommandHandlerResolverTest extends ChiefTestCase
{
    public function testInstantiable()
    {
        $this->assertTrue(new NativeCommandHandlerResolver(new NativeContainer) instanceof CommandHandlerResolver);
    }

    public function testResolveThrowsExceptionWhenNoHandlerFound()
    {
        $resolver = new NativeCommandHandlerResolver(new NativeContainer);
        $this->setExpectedException('Chief\Exceptions\UnresolvableCommandHandlerException');
        $resolver->resolve('Chief\Stubs\TestCommandWithoutHandler');
    }

    public function testResolveReturnsHandlerWhenWithHandlerSuffix()
    {
        $resolver = new NativeCommandHandlerResolver(new NativeContainer);
        $handler = $resolver->resolve('Chief\Stubs\TestCommand');
        $this->assertTrue($handler instanceof TestCommandHandler);
    }
}
