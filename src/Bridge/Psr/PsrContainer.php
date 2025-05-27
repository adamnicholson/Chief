<?php

namespace Chief\Bridge\Psr;

use Chief\Container;
use Psr\Container\ContainerInterface;

class PsrContainer implements Container
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
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
