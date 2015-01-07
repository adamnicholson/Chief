<?php

namespace Chief\Containers;

use Chief\ChiefTestCase;

class NativeContainerTest extends ChiefTestCase
{
    public function testMake()
    {
        $container = new NativeContainer();
        $made = $container->make('stdClass');
        $this->assertTrue($made instanceof \stdClass);
    }
}