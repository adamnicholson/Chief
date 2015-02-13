<?php

namespace Chief;

interface CommandQueuer
{
    /**
     * Queue a Command for executing
     *
     * @param Command $command
     */
    public function queue(Command $command);
}
