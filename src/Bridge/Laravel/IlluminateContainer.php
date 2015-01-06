<?php

namespace Chief\Bridge\Laravel;

use Chief\Container;
use Illuminate\Foundation\Application;

class IlluminateContainer implements Container
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Instantiate and return an object based on its class name
     * 
     * @param $class
     * @return object
     */
    public function make($class)
    {
        return $this->app->make($class);
    }
}