<?php

namespace Chief;

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
        $resolver->resolve('ChiefTestCommandWithoutHandlerStub');
    }
}