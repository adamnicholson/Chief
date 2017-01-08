<?php

namespace Chief\Decorator;

use Chief\CommandBus;

trait InnerBusTrait
{
    /**
     * @var \Chief\CommandBus
     */
    protected $innerCommandBus;

    public function setInnerBus(CommandBus $bus)
    {
        $this->innerCommandBus = $bus;
    }
}
