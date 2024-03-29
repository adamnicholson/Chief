<?php

namespace Chief\Bridge\Laravel;

use Chief\ChiefTestCase;
use Illuminate\Container\Container;

class IlluminateContainerTest extends ChiefTestCase
{
    public function testMakeHitsInnerContainer()
    {
        $container = new IlluminateContainer($inner = $this->createMock('Illuminate\Container\Container'));
        $container->expects($this->once())->method('make')->with('stdClass')->willReturn(new \stdClass());
        $made = $container->make('stdClass');
        $this->assertTrue($made instanceof \stdClass);
    }

    public function testMakeReturnsExpectedInstance()
    {
        $container = new IlluminateContainer(new Container);
        $made = $container->make('stdClass');
        $this->assertTrue($made instanceof \stdClass);
    }
}