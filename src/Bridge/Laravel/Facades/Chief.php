<?php

namespace Chief\Bridge\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

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
