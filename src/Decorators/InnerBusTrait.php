<?php

namespace Chief\Decorators;

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
