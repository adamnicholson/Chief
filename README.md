#Chief

Chief is a command bus package for PHP 5.3+.

## Usage

We'll use the 2 below classes for the usage examples:

    use Chief\Chief;
    use Chief\Command;
    use Chief\CommandHandler;

    class MyCommand implements Command {}
    class MyCommandHandler implements CommandHandler {
        public function handle(Command $command) { /* ... */ }
    }



When you pass a `Command` to `Chief::execute()`, Chief will automatically search for the relevant `CommandHandler`

    $chief = new Chief;
    $chief->execute(new MyCommand);

Or if you'd prefer to explicitally bind a handler to a command, you can::

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


## CommandHandler dependency injection
If you are using an IoC Container within your application, you can inject this into Chief, which will use your container when creating new `CommandHandler` instances. This is useful in situations where you need dependency injection into your handlers.

Using Laravel's IoC Container:

    class IlluminateContainer implements Chief\Container {
        public function make($class) {
            return App::make($class);
        }
    }

    $chief = new Chief(new LaravelContainer);
    $chief->pushHandler('MyCommand', 'MyCommandHandler');
    $chief->execute(new MyCommand);


