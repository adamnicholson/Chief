<?php

namespace Chief\Bridge\League;

use Chief\ChiefTestCase;

class LeagueContainerTest extends ChiefTestCase
{
    public function testMakeHitsInnerContainer()
    {
        $container = new LeagueContainer($inner = $this->getMockBuilder('League\Container\Container')->getMock());
        $container->expects($this->once())->method('get')->with('stdClass')->willReturn(new \stdClass());
        $made = $container->make('stdClass');
        $this->assertTrue($made instanceof \stdClass);
    }

    public function testMakeReturnsExpectedInstance()
    {
        $container = new LeagueContainer(new \League\Container\Container());
        $made = $container->make('stdClass');
        $this->assertTrue($made instanceof \stdClass);
    }
}