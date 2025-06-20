<?php

namespace Chief\Decorators;

use Chief\Busses\SynchronousCommandBus;
use Chief\Command;
use Chief\CommandBus;
use Chief\Decorator;
use Psr\Log\LoggerInterface;

class LoggingDecorator implements Decorator
{
    use InnerBusTrait;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var mixed|null
     */
    protected $context;

    /**
     * @param LoggerInterface $logger
     * @param mixed $context Something which is serializable that will be logged with
     * the command execution, such as the request/session information.
     * @param CommandBus $innerCommandBus
     */
    public function __construct(LoggerInterface $logger, mixed $context = null, ?CommandBus $innerCommandBus = null)
    {
        $this->logger = $logger;
        $this->context = $context;
        $this->setInnerBus($innerCommandBus ?: new SynchronousCommandBus());
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
        $this->log('Executing command [' . get_class($command) . ']', $command);

        try {
            $response = $this->innerCommandBus->execute($command);
        } catch (\Exception $e) {

            $message = 'Failed executing command [' . get_class($command) . ']. ' . $this->createExceptionString($e);
            $this->log($message, $command);
            throw $e;
        }

        $this->log('Successfully executed command [' . get_class($command) . ']', $command);

        return $response;
    }

    protected function log($message, $command)
    {
        $context = $this->context ? serialize($this->context) : null;
        $this->logger->debug($message, ['Command' => serialize($command), 'Context' => $context]);
    }

    protected function createExceptionString(\Exception $e)
    {
        return 'Uncaught ' . get_class($e) . '[' . $e->getMessage() . '] throw in ' . $e->getFile() .
        ' on line ' . $e->getLine() . '. Stack trace: ' . $e->getTraceAsString();
    }
}
