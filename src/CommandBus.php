<?php

namespace Chief;

interface CommandBus
{
    /**
     * Execute a command
     *
     * @param Command $command
     * @return mixed
     */
    public function execute(Command $command);
}