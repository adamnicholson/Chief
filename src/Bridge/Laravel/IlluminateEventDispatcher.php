<?php

namespace Chief\Bridge\Laravel;

use Chief\Decorators\EventDispatcher;
use Illuminate\Events\Dispatcher;

class IlluminateEventDispatcher implements EventDispatcher
{
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $event The event name
     * @param array $data The event data
     * @return void
     */
    public function dispatch($event, $data = [])
    {
        $this->dispatcher->fire($event, $data);
    }
}