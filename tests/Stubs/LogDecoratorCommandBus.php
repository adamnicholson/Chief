<?php

namespace Chief\Stubs;

use Chief\Command;
use Chief\CommandBus;
use Psr\Log\LoggerInterface;

class LogDecoratorCommandBus implements CommandBus
{
    public function __construct(LoggerInterface $logger, CommandBus $commandBus)
    {
        $this->logger = $logger;
        $this->commandBus = $commandBus;
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