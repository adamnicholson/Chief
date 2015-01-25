<?php

namespace Chief\Handlers;

use Chief\Command;
use Chief\CommandHandler;
use Chief\Container;

class LazyLoadingCommandHandler implements CommandHandler
{
    /**
     * @var \Chief\Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $handler;

    /**
     * @param string $handlerName
     * @param Container $container
     */
    public function __construct($handlerName, Container $container)
    {
        $this->container = $container;
        $this->handlerName = $handlerName;
    }

    /**
     * Handle a command execution
     *
     * @param Command $command
     * @return mixed
     */
    public function handle(Command $command)
    {
        $handler = $this->container->make($this->handlerName);

        return $handler->handle($command);
    }
}
