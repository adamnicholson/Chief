<?php

namespace Chief\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends ChiefException implements NotFoundExceptionInterface
{
}
