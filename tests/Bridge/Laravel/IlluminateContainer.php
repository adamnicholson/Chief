<?php

namespace Chief\Bridge\Laravel;

use Chief\ChiefTest;
use Illuminate\Container\Container;

class IlluminateContainerTest extends ChiefTest
{
    public function testMakeHitsInnerContainer()
    {
        $container = new IlluminateContainer($inner = $this->getMock('Illuminate\Container\Container'));
        $container->expects($this->once())->with('stdClass')->willReturn(new \stdClass());
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