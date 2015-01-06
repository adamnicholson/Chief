<?php

namespace Chief;

interface Decorator extends CommandBus
{
    /**
     * Set the CommandBus which we're decorating
     *
     * @param CommandBus $bus
     * @return mixed
     */
    public function setInnerBus(CommandBus $bus);
}
