<?php

namespace Chief\Decorators;

use Chief\Command;
use Chief\CommandBus;
use Chief\Decorator;
use Psr\Log\LoggerInterface;

class LoggingDecorator implements Decorator
{
    public function __construct(LoggerInterface $logger, CommandBus $innerCommandBus = null)
    {
        $this->logger = $logger;
        $this->innerCommandBus = $innerCommandBus;
    }

    public function setInnerBus(CommandBus $bus)
    {
        $this->innerCommandBus = $bus;
    }

    /**
     * Execute a command
     *
     * @param Command $command
     * @return mixed
     */
    public function execute(Command $command)
    {
        $this->log('Executing command [' . get_class($command) . ']', [$command]);

        $this->innerCommandBus->execute($command);

        $this->log('Successfully executed command [' . get_class($command) . ']', [$command]);
    }

    protected function log($message, $context = [])
    {
        $this->logger->info($message, $context);
    }
}