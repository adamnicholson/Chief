<?php

namespace Chief\Bridge\Laravel;

use Chief\Container;

class IlluminateContainer implements Container
{
    /**
     * @var \Illuminate\Container\Container
     */
    protected $container;

    public function __construct(\Illuminate\Container\Container $container)
    {
        $this->container = $container;
    }

    /**
     * Instantiate and return an object based on its class name
     *
     * @param $class
     * @return object
     */
    public function make($class)
    {
        return $this->container->make($class);
    }
}
