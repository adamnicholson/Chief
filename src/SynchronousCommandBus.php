<?php

namespace Chief;

use Chief\Command;
use Chief\CommandBus;
use Chief\CommandHandlerResolver;
use Chief\Resolvers\NativeCommandHandlerResolver;

class SynchronousCommandBus implements CommandBus
{
    protected $resolver;

    public function __construct(CommandHandlerResolver $resolver = null)
    {
        $this->resolver = $resolver ?: new NativeCommandHandlerResolver;
    }

    /**
     * Execute a command
     *
     * @param Command $command
     * @return mixed
     */
    public function execute(Command $command)
    {
        $handler = $this->resolver->resolve($command);

        return $handler->handle($command);
    }
}
