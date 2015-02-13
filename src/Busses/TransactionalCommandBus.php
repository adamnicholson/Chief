<?php

namespace Chief\Busses;

use Chief\Decorator;
use Chief\Decorators\TransactionalCommandLockingDecorator;

/**
 * @deprecated Use \Chief\Decorators\TransactionalCommandLockingDecorator
 */
class TransactionalCommandBus extends TransactionalCommandLockingDecorator implements Decorator {}
