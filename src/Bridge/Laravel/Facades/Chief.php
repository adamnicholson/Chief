<?php

namespace Chief\Bridge\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed execute(\Chief\Command $command) Execute a command
 */
class Chief extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'Chief\Chief';
    }
}
