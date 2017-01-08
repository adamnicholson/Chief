<?php

namespace Chief\Decorator\Event;

interface EventDispatcher
{
    /**
     * @param string $event The event name
     * @param array $data The event data
     * @return void
     */
    public function dispatch($event, $data = []);
}
