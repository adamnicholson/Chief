<?php

namespace Chief\Bridge\League;

use Chief\Container;

class LeagueContainer implements Container
{
    public function __construct(\League\Container\Container $container)
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
        return $this->container->get($class);
    }
}