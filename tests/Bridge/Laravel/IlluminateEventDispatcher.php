<?php

namespace Chief\Bridge\Laravel;

use Chief\ChiefTest;
use Chief\Stubs\TestCommand;
use Illuminate\Container\Container;

class IlluminateEventDispatcherTest extends ChiefTest
{
    public function testDispatchHitsDispatcher()
    {
        $instance = new IlluminateEventDispatcher($dispatcher = $this->getMock('Illuminate\Events\Dispatcher'));
        $eventName = 'Foo.Event';
        $eventdata = new TestCommand();
        $dispatcher->expects($this->once())->method('fire')->with($eventName, $eventdata);
        $instance->dispatch($eventName, $eventdata);
    }
}