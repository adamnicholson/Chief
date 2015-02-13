<?php

namespace Chief\Busses;

use Chief\Decorator;
use Chief\Decorators\CommandQueueingDecorator;

/**
 * @deprecated Use Chief\Decorators\CommandQueueingDecorator
 */
class QueueingCommandBus extends CommandQueueingDecorator implements Decorator {}
