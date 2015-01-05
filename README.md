#Chief

[![Build Status](https://travis-ci.org/adamnicholson/chief.svg?branch=master)](https://travis-ci.org/adamnicholson/chief)

Chief is a lightweight command bus package for PHP 5.4+.

## Installation

Install the latest version with `composer require adamnicholson/chief`, or see [Packagist](https://packagist.org/packages/adamnicholson/chief).

## Features

- Handle commands via CommandHandler classes or anonymous functions
- Self-handling commands
- Command bus decorators
- Queued commands
- Lightweight interface
- Framework agnostic

## Command Bus?

A Command Bus is an object oriented design pattern which involved 3 classes:

1. `Command`
2. `CommandHandler`
3. `CommandBus`

In a nutshell, a `Command` is just a tiny object containing some data (either in public properties or in getters/setters). You pass your `Command` object to the `execute($command)` method on the `CommandBus`, which is responsible for further passing the `Command` to the `handle()` method on its `CommandHandler`.

For every `Command` in your application, there should be a corresponding `CommandHandler`.

In the below example, we demonstrate how a command bus design could handle registering a new user in your system:


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

By default, this will search for a `CommandHandler` class with the same name as your `Command`, suffixed with 'Handler'. For example, if your `Command` class is called `FooCommand`, Chief will look for a `FooCommandHandler` class when you call `execute($command)`. If it finds the handler, it will be automatically instantiated and `handle($command)` will be called on it.

Implement your own version of the `Chief\CommandHandlerResolver` interface to modify the default automatic resolution behaviour.
    
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

Chief provides you with a number of decorators out-the-box:

- *LoggingDecorator*: Log before and after all executions to a `Psr\Log\LoggerInterface`
- *EventDispatchingDecorator*: Dispatch an event to a `Chief\Decorators\EventDispatcher` after every command execution.

Registering a decorator:

    $chief = new Chief(new SynchronousCommandBus, [new LoggingDecorator($logger)]);
    
Registering multiple decorators:

    $chief = new Chief(new SynchronousCommandBus, [
        new LoggingDecorator($logger),
        new EventDispatchingDecorator($eventDispatcher)
    ]);
    

## Dependency Injection Container Integration
Chief uses a `CommandHandlerResolver` class which is responsible for finding and instantiating the relevant `CommandHandler` for a given `Command`. 

If you want to use your own Dependency Injection Container to control the actual instantiation, just create your own class which implements `Chief\Container` and pass it to the `CommandHandlerResolver`.

For example, if you're using Laravel:

    class IlluminateContainer implements Chief\Container {
        public function make($class) {
            return App::make($class);
        }
    }
    
	$resolver = new Chief\NativeCommandHandlerResolver(new IlluminateContainer);
    $chief = new Chief($resolver);
    $chief->execute(new MyCommand);
    

## Author

Adam Nicholson - adamnicholson10@gmail.com

## Contributing

We welcome any contributions to Chief. They can be made via GitHub issues or pull requests.

## License

Chief is licensed under the MIT License - see the `LICENSE.txt` file for details