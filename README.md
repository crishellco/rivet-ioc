# RivetIoc

RivetIoc is an auto-wiring (zero configuration) IoC container for PHP to easily manage class dependencies. It features both auto-wiring of dependencies using reflection as well as manual registration for depenency injection.

## Features

* Auto-wiring (zero configuration) dependency injection
  * Recursive dependency injection
* Manual registration for more complex dependency management
* Locator trait which exposes commonly used RivetIoc\Ioc methods

## Getting Started

### Install

````
composer require crishellco/rivet-ioc
````

### System Requirements

**PHP >= 5.4.0**

## Documentation

### Auto-wiring

**Define your classes using type hints**

````php
namespace App;
class DbDriver
{
    ...
}
````
````php
namespace App;
class Db
{
    protected $driver;

    public function __construct(DbDriver $driver)
    {
        $this->driver = $driver;
    }
}
````
````php
namespace App;
class UserService
{
    protected $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }
}
````

**Use RivetIoc to create a new class instance**

RivetIoc will use constructor type hints to automatically create and inject dependencies

````php
$userService = \RivetIoc\Ioc::getInstance()->make('App\UserService');
````

### Manual dependency registration

**Register your dependencies in your application bootstrap**

````php
RivetIoc\Ioc::register('App\Db', function() {
    $mysqli = new mysqli('localhost', 'username', 'password', 'mydb');
    $db = new App\Db($mysqli);
    
    return $db;
});
````

**Use RivetIoc to create a new class instance**

RivetIoc will use the registered closure to create and inject dependencies

````php
$db = \RivetIoc\Ioc::getInstance()->make('App\Db');
````

### Locator trait

Use the RivetIoc\Traits\Locator trait in a class give access to commonly used RivetIoc\Ioc methods:

* make
* register
* unregister

## How to Contribute

### Pull Requests

1. Fork the RivetIoc repository
2. Create a new branch for each feature or improvement
3. Send a pull request from each feature branch to the **develop** branch
