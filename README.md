#Chief

[![Build Status](https://travis-ci.org/adamnicholson/chief.svg?branch=master)](https://travis-ci.org/adamnicholson/chief)

Chief is a lightweight command bus package for PHP 5.4+.

## Features

- Handle commands via CommandHandler classes or anonymous functions
- Self-handling commands
- Command bus decorators
- Queued commands
- Lightweight interface
- Framework agnostic

## Command Bus?


> The most common style of interface to a module is to use procedures, or object methods. So if you want a module to calculate a bunch of charges for a contract, you might have a BillingService class with a method for doing the calculation, calling it like this `$billingService->calculateCharges($contract)`. A command oriented interface would have a command class for each operation, and be called with something like this `(new CalculateChargesCommand($contract)->execute()`. Essentially you have one command class for each method that you would have in the method-oriented interface. A common variation is to have a separate command executor object that actually does the running of the command. `$command = new CalculateChargesCommand($contract); $commandBus->execute($command);`

-- From [Martin Fowler's Blog](http://martinfowler.com/bliki/CommandOrientedInterface.html) (*code samples haven ported to PHP*):

That 'executor' Martin mentions is what we call the command bus. The pattern typically consists of 3 classes:

1. `Command`: A tiny object containing some data (probably just some public properties or getters/setters)
2. `CommandHandler`: Responsible for running the command through a `handle($command)` method
3. `CommandBus`: All commands are passed to the bus `execute($command)` method, which is responsible for finding the right `CommandHandler` and calling the `handle($command)` method.

For every `Command` in your application, there should be a corresponding `CommandHandler`.

In the below example, we demonstrate how a command bus design could handle registering a new user in your system using Chief as your command bus:


	use Chief\Command;
	use Chief\CommandHandler;
	use Chief\Chief;
	
	class RegisterUserCommand implements Chief {
		public $email;
		public $name;
	}
	
	class RegisterUserCommandHandler implements CommandHandler {
		public function handle(Command $command) {
			Users::create([
				'email' => $command->email,
				'name' => $command->name
			]);
			Mailer::sendWelcomeEmail($command->email);
		}
	}
	
	$chief = new Chief;

	$registerUserCommand = new RegisterUserCommand;
	$registerUserCommand->email = 'adamnicholson10@gmail.com';
	$registerUserCommand->name = 'Adam Nicholson';

	$chief->execute($registerUserCommand);



## Installation

Install the latest version with `composer require adamnicholson/chief`, or see [Packagist](https://packagist.org/packages/adamnicholson/chief).

No further setup is required, however if you're using a framework and want to make sure that we play nicely (with DI Containers, Event handlers, etc), then use the bridges below.

#### Laravel

After installing via composer, add the below to the `$providers` array in your `app/config/app.php`:

    'Chief\Bridge\Laravel\LaravelServiceProvider'

## Usage

#### Example command and handler
We'll use the 2 below classes for the usage examples:

    use Chief\Chief;
    use Chief\Command;
    use Chief\CommandHandler;
    
    class MyCommand implements Command {}
    class MyCommandHandler implements CommandHandler {
        public function handle(Command $command) { /* ... */ }
    }
    
   
#### Automatic handler resolution

When you pass a `Command` to `Chief::execute()`, Chief will automatically search for the relevant `CommandHandler` and call the `handle()` method:

    $chief = new Chief;
    $chief->execute(new MyCommand);

By default, this will search for a `CommandHandler` class with the same name as your `Command`, suffixed with 'Handler'. For example, if your `Command` class is called `FooCommand`, Chief will look for a `FooCommandHandler` class, instantiate it and call `handle($command)`.

Want to implement your own method of automatically resolving handlers from commands? Implement your own version of the `Chief\CommandHandlerResolver` interface to modify the automatic resolution behaviour.
    
#### Handlers bound by class name

If your handlers don't follow a particular naming convention, you can explicitly bind a command to a handler by its class name:

	$resolver = new Chief\NativeCommandHandlerResolver();
	$chief = new Chief($resolver);
	
	$resolver->bindHandler('MyCommand', 'MyCommandHandler');

    $chief->execute(new MyCommand);
    
#### Handlers bound by object

Or, just pass your `CommandHandler` instance:
    
    $resolver = new Chief\NativeCommandHandlerResolver();
	$chief = new Chief($resolver);
	
	$resolver->bindHandler('MyCommand', new MyCommandHandler);

    $chief->execute(new MyCommand);
    
#### Handlers as anonymous functions

Sometimes you might want to quickly write a handler for your `Command` without having to write a new class. With Chief you can do this by passing an anonymous function as your handler:

    $resolver = new Chief\NativeCommandHandlerResolver();
	$chief = new Chief($resolver);
	
	$resolver->bindHandler('MyCommand', function (Command $command) {
        /* ... */
    });

    $chief->execute(new MyCommand);
    
#### Self-handling commands

Alternatively, you may want to simply allow a `Command` object to execute itself. To do this, just ensure your `Command` class also implements `CommandHandler`:

    class SelfHandlingCommand implements Command, CommandHandler {
        public function handle(Command $command) { /* ... */ }
    }
    $chief = new Chief;
    $chief->execute(new MyCommand);

## Decorators
Imagine you want to log every command execution. You could do this by adding a call to your logger in every `CommandHandler`, however a much more elegant solution is to use decorators.

Registering a decorator:

    $chief = new Chief(new SynchronousCommandBus, [new LoggingDecorator($logger)]);
    
Now, whenever `Chief::execute()` is called, the command will be passed to `LoggingDecorator::execute()`, which will perform some log action, and then pass the command to the relevant `CommandHandler`.

Chief provides you with two decorators out-the-box:

- *LoggingDecorator*: Log before and after all executions to a `Psr\Log\LoggerInterface`
- *EventDispatchingDecorator*: Dispatch an event to a `Chief\Decorators\EventDispatcher` after every command execution.
    
Registering multiple decorators:

    $chief = new Chief(new SynchronousCommandBus, [
        new LoggingDecorator($logger),
        new EventDispatchingDecorator($eventDispatcher)
    ]);
    
## Queued Commands

By default, commands are executed by the `SynchronousCommandBus`, which handles them straight away. You may however wish to queue commands to be executed later. This is where the `QueueingCommandBus` comes in.

To use the `QueueingCommandBus`, you must first implement the `CommandBusQueuer` interface with your desired Queue package:

    interface CommandBusQueuer
    {
        /**
         * Queue a Command for executing
         *
         * @param Command $command
         */
        public function queue(Command $command);
    }

Next, inject the `QueueingCommandBus` when you start up Chief:

    $queuer = MyCommandBusQueuer();
    $bus = new QueueingCommandBus($queuer);
    $chief = new Chief($bus);
    
Then use Chief as normal:

    $command = new MyCommand();
    $chief->execute($command);
    
An implementation of this interface for illuminate/queue is [included](https://github.com/adamnicholson/Chief/blob/master/src/Bridge/Laravel/IlluminateQueuer.php).


## Dependency Injection Container Integration
Chief uses a `CommandHandlerResolver` class which is responsible for finding and instantiating the relevant `CommandHandler` for a given `Command`. 

If you want to use your own Dependency Injection Container to control the actual instantiation, just create your own class which implements `Chief\Container` and pass it to the `CommandHandlerResolver` which is consumed by `SynchronousCommandBus`.

For example, if you're using Laravel:

    class IlluminateContainer implements Chief\Container {
        public function make($class) {
            return App::make($class);
        }
    }
    
	$resolver = new Chief\NativeCommandHandlerResolver(new IlluminateContainer);
	$bus = new SynchronousCommandBus($resolver);
    $chief = new Chief($bus);
    $chief->execute(new MyCommand);
    
## Integration


## Author

Adam Nicholson - adamnicholson10@gmail.com

## Contributing

We welcome any contributions to Chief. They can be made via GitHub issues or pull requests.

## License

Chief is licensed under the MIT License - see the `LICENSE.txt` file for details