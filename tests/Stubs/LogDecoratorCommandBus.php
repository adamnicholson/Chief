<?php

namespace Chief\Stubs;

use Chief\Command;
use Chief\CommandBus;
use Chief\Decorator;
use Psr\Log\LoggerInterface;

class LogDecoratorCommandBus implements Decorator
{
    public function __construct(LoggerInterface $logger, CommandBus $commandBus)
    {
        $this->logger = $logger;
        $this->commandBus = $commandBus;
    }

    /**
     * Set the CommandBus which we're decorating
     *
     * @param CommandBus $bus
     * @return mixed
     */
    public function setInnerBus(CommandBus $bus)
    {
        $this->commandBus = $bus;
    }


    /**
     * Execute a command
     *
     * @param Command $command
     * @return mixed
     */
    public function execute(Command $command)
    {
        $this->logger->info('Started executing command [' . get_class($command) . ']');
        $response = $this->commandBus->execute($command);
        $this->logger->info('Finished executing command [' . get_class($command) . ']');
        return $response;
    }
}