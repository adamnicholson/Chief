<?php

namespace Chief;

interface Container
{
    /**
     * Instantiate and return an object based on its class name
     *
     * @param $class
     * @return object
     */
    public function make($class);
}
