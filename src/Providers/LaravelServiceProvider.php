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
        $this->app->bind('Chief\Container', 'Chief\Containers\IlluminateContainer');

        $this->app->bind('Chief\CommandBus', function () {
            $resolver = new NativeCommandHandlerResolver($this->app->make('Chief\Container'));
            $defaultBus = new SynchronousCommandBus($resolver);
            return new Chief($defaultBus);
        });
    }
}