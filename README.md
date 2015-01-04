#Chief

[![Build Status](https://travis-ci.org/adamnicholson/chief.svg?branch=master)](https://travis-ci.org/adamnicholson/chief)

Chief is a lightweight command bus package for PHP 5.4+.

## Installation

Install the latest version with `composer require adamnicholson/chief`, or see [Packagist](https://packagist.org/packages/adamnicholson/chief).

## Features

- Handle commands via CommandHandler classes or anonymous functions
- Support for self-handling commands
- Support for decorators
- Lightweight interface
- Framework agnostic

## Command Bus?

A Command Bus is an object oriented design pattern which involved 3 classes:

1. `Command`
2. `CommandHandler`
3. `CommandBus`

In a nutshell, a `Command` is just a tiny object containing some data (either in public properties or in getters/setters). This `Command` is then passed the `execute()` method on the `CommandBus`, which is responsible for further passing the `Command` to the `handle()` method on a `CommandHandler`.

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

We'll use the 2 below classes for the usage examples:

    use Chief\Chief;
    use Chief\Command;
    use Chief\CommandHandler;
    
    class MyCommand implements Command {}
    class MyCommandHandler implements CommandHandler {
        public function handle(Command $command) { /* ... */ }
    }
    
    

When you pass a `Command` to `Chief::execute()`, Chief will automatically search for the relevant `CommandHandler` and call the `handle()` method:

    $chief = new Chief;
    $chief->execute(new MyCommand);
    
Or if you'd prefer to explicitly bind a command to a handler, you can:

    $chief = new Chief;
    $chief->pushHandler('MyCommand', 'MyCommandHandler');
    $chief->execute(new MyCommand);
    
Or, just inject your own `CommandHandler` instance manually:
    
    $chief = new Chief;
    $chief->pushHandler('MyCommand', new MyCommandHandler);
    $chief->execute(new MyCommand);
    
Sometimes you might want to quickly write a handler for your `Command` without having to write a new class. With Chief you can do this by passing an anonymous function as your handler:

    $chief = new Chief;
    $chief->pushHandler('MyCommand', function (Command $command) {
        // Do something with your $command
    });
    $chief->execute(new MyCommand);
    
Alternatively, you may want to simply allow a `Command` object to execute itself. You can do this easily by ensuring your `Command` class also implements `CommandHandler`:

    class SelfHandlingCommand implements Command, CommandHandler {
        public function handle(Command $command) { /* ... */ }
    }
    $chief = new Chief;
    $chief->execute(new MyCommand);

## Decorators
Imagine you want to log every command execution. You could do this by adding a call to your logger in every `CommandHandler`, however a much more elegant solution is to use decorators. 

All Chief decorators must implement the `CommandBus` interface. For the Log example, you may create a decorator which looks like this:

    class LogDecorator implements CommandBus {
        public function execute(Chief\Command $command) {
            Log::debug($command);
        }
    }
    
    $chief = new Chief;
    $chief->addDecorator(new LogDecorator);
    $chief->execute(new MyCommand);

The `execute()` method on all decorators will be called before the `CommandHandler` `handle()` method is called.


## Dependency Injection Container Integration
Want to use your own Dependency Injection Container? No problem, just create your own class which implements `Chief\Container` and pass it to Chief.

For example, if you're using Laravel:

    class IlluminateContainer implements Chief\Container {
        public function make($class) {
            return App::make($class);
        }
    }
    
    $chief = new Chief(new LaravelContainer);
    $chief->pushHandler('MyCommand', 'MyCommandHandler');
    $chief->execute(new MyCommand);

## Interfaces

#### CommandBus
`Chief\CommandBus` is the main CommandBus which `Chief\Chief` implements:

    interface CommandBus
    {
        /**
         * Execute a command
         *
         * @param Command $command
         * @return mixed
         */
        public function execute(Command $command);
    
        /**
         * Map a command to a callable handler
         *
         * @param string $commandName
         * @param CommandHandler|callable|string $handler
         * @return mixed
         */
        public function pushHandler($commandName, $handler);
    
        /**
         * Add a decorator
         *
         * @param CommandBus $decorator
         * @return mixed
         */
        public function addDecorator(CommandBus $decorator);
    }

#### Command
`Chief\Command` should be implemented by all `Command` classes, although the interface is empty due to the nature of command objects:

    interface Command {}

#### CommandHelper
`Chief\CommandHandler` takes in `Chief\Command` objects and is responsible for handling the desired operation:

    interface CommandHandler
    {
        /**
         * Handle a command execution
         *
         * @param Command $command
         * @return mixed
         */
        public function handle(Command $command);
    }

#### Container
`Chief\Container` is responsible for instantiating objects based on their class name/alias:

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

#### CommandHandlerResolver
`Chief\CommandHandlerResolver` is responsible for automatically finding a `CommandHandler` which has not been explicitly bound to a `Command` via `pushHandler()`:
    
    interface CommandHandlerResolver
    {
        /**
         * Automatically resolve a handler from a command
         *
         * @param string $command
         * @return CommandHandler
         */
        public function resolve($command);
    }
    
The default `CommandHandlerResolver` will attempt to find handlers with the same name as the `Command` given, suffixed with `Handler`. For example, given "FooCommand", the resolver will return "FooCommandHandler" if it exists.