<?php

namespace Chief\Handlers;

use Chief\Command;
use Chief\CommandHandler;
use Chief\Container;

class StringCommandHandler implements CommandHandler
{
    /**
     * @var \Chief\Container
     */
    protected $container;

    /**
     * @var \Chief\CommandHandler
     */
    protected $handler;

    public function __construct($string, Container $container)
    {
        $this->container = $container;

        $this->handler = $this->container->make($string);
    }

    /**
     * Handle a command execution
     *
     * @param Command $command
     * @return mixed
     */
    public function handle(Command $command)
    {
        return $this->handler->handle($command);
    }

}