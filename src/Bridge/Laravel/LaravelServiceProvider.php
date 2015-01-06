<?php

namespace Chief\Bridge\Laravel;

use Chief\Busses\QueueingCommandBus;
use Chief\Busses\SynchronousCommandBus;
use Chief\Chief;
use Chief\Resolvers\NativeCommandHandlerResolver;
use Illuminate\Support\ServiceProvider;

class LaravelServiceProvider extends ServiceProvider
{
    protected $defaultBus = 'Chief\Busses\SynchronousCommandBus';

    public function register()
    {
        $this->registerContainer();

        $this->registerCommandHandlerResolver();

        $this->registerEventDispatcher();

        $this->registerBusses();

        $this->registerChief();
    }

    /**
     * Override me to easily add decorators
     *
     * @return array Array of \Chief\Decorator objects
     */
    protected function getDecorators()
    {
        return [];
    }

    protected function registerContainer()
    {
        $this->app->bind('Chief\Container', 'Chief\Bridge\Laravel\IlluminateContainer');
    }

    protected function registerCommandHandlerResolver()
    {
        $this->app->bind('Chief\CommandHandlerResolver', function () {
            return new NativeCommandHandlerResolver($this->app->make('Chief\Container'));
        });
    }

    protected function registerBusses()
    {
        $this->registerSyncCommandBus();
        $this->registerQueueCommandBus();
    }

    protected function registerSyncCommandBus()
    {
        $this->app->bind('Chief\Busses\SynchronousCommandBus', function () {
            $resolver = $this->app->make('Chief\CommandHandlerResolver');
            return new SynchronousCommandBus($resolver);
        });
    }

    protected function registerQueueCommandBus()
    {
        $this->app->bind('Chief\Busses\QueueingCommandBus', function () {
            $resolver = $this->app->make('Chief\CommandHandlerResolver');
            return new QueueingCommandBus($this->app->make('Chief\Bridge\Laravel\IlluminateQueuer'), $resolver);
        });
    }

    protected function registerEventDispatcher()
    {
        $this->app->bind('Chief\Decorators\EventDispatcher', 'Chief\Bridge\Laravel\IlluminateEventDispatcher');
    }

    protected function registerChief()
    {
        $this->app->bind('Chief\CommandBus', function () {
            $bus = $this->app->make($this->defaultBus);
            return new Chief($bus, $this->getDecorators());
        });
    }
}