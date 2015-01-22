<?php

namespace Chief\Decorators;

use Chief\Command;
use Chief\CommandBus;
use Chief\Decorator;
use Psr\Log\LoggerInterface;

class LoggingDecorator implements Decorator
{
    protected $innerCommandBus;
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param CommandBus $bus
     * @return void
     */
    public function setInnerBus(CommandBus $bus)
    {
        $this->innerCommandBus = $bus;
    }

    /**
     * Execute a command
     *
     * @param Command $command
     * @throws \Exception
     * @return mixed
     */
    public function execute(Command $command)
    {
        $this->log('Executing command [' . get_class($command) . ']', [$command]);

        try {
            $this->innerCommandBus->execute($command);
        } catch (\Exception $e) {
            $this->log('Failed executing command [' . get_class($command) . ']. ' .
                $this->createExceptionString($e), [$command]);
            throw $e;
        }

        $this->log('Successfully executed command [' . get_class($command) . ']', [$command]);
    }

    protected function log($message, $context = [])
    {
        $this->logger->debug($message, $context);
    }

    protected function createExceptionString(\Exception $e)
    {
        return 'Uncaught ' . get_class($e) . '[' . $e->getMessage() . '] throw in ' . $e->getFile() .
        ' on line ' . $e->getLine() . '. Stack trace: ' . $e->getTraceAsString();
    }
}
