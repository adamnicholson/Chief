<?php

namespace Chief\Containers;

use Chief\Container;
use Chief\Exceptions\NotFoundException;

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
    
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Class $id not found");
        }
        return $this->make($id);
    }

    public function has(string $id): bool
    {
        return class_exists($id);
    }
}
