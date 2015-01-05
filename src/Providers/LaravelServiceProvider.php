<?php

namespace Chief\Providers;

use Chief\Busses\SynchronousCommandBus;
use Chief\Chief;
use Chief\Resolvers\NativeCommandHandlerResolver;
use Illuminate\Support\ServiceProvider;

class LaravelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerContainer();

        $this->registerCommandBus();

        $this->registerEventDispatcher();
    }

    protected function registerContainer()
    {
        $this->app->bind('Chief\Container', 'Chief\Containers\IlluminateContainer');
    }

    protected function registerCommandBus()
    {
        $this->app->bind('Chief\CommandBus', function () {
            $resolver = new NativeCommandHandlerResolver($this->app->make('Chief\Container'));
            $defaultBus = new SynchronousCommandBus($resolver);
            return new Chief($defaultBus);
        });
    }

    protected function registerEventDispatcher()
    {
        $this->app->bind('Chief\Decorators\EventDispatcher', 'Chief\Decorators\Laravel\IlluminateEventDispatcher');
    }
}