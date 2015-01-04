<?php

namespace Chief;

interface CommandBusQueuer
{
    /**
     * Queue a Command for executing
     *
     * @param Command $command
     * @return mixed
     */
    public function queue(Command $command);
}