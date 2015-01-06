<?php

namespace Chief;

interface CommandBusQueuer
{
    /**
     * Queue a Command for executing
     *
     * @param Command $command
     */
    public function queue(Command $command);
}
