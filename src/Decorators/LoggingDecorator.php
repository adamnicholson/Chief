<?php

namespace Chief\Decorators;

use Chief\Command;
use Chief\CommandBus;
use Psr\Log\LoggerInterface;

class LoggingDecorator implements CommandBus
{
    public function __construct(LoggerInterface $logger, CommandBus $innerCommandBus)
    {
        $this->logger = $logger;
        $this->innerCommandBus = $innerCommandBus;
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