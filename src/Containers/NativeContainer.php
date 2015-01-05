<?php

namespace Chief\Containers;

use Chief\Container;

class NativeContainer implements Container
{
    /**
     * Instantiate and return an object based on its class name
     * 
     * @param $class
     * @return object
     */
    public function make($class)
    {
        return new $class;
    }
}