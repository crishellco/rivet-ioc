# RivetIoc

[![Build Status](https://travis-ci.org/crishellco/rivet-ioc.svg?branch=master)](https://travis-ci.org/crishellco/rivet-ioc)

RivetIoc is an auto-wiring (zero configuration) IoC container for PHP to easily manage class dependencies. It features both auto-wiring of dependencies using reflection as well as manual registration for depenency injection.

## Features

* Auto-wiring (zero configuration) dependency injection
  * Recursive dependency injection
* Manual registration for more complex dependency management
* Locator trait which exposes commonly used RivetIoc\Ioc methods

## Getting Started

### Install

````
$ composer require crishellco/rivet-ioc
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
namespace App\Services;
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
$userService = \RivetIoc\Ioc::getInstance()->make('App\Services\UserService');
````

### Manual dependency registration

**Register your dependencies in your application bootstrap**

````php
\RivetIoc\Ioc::getInstance()->register('App\Db', function() {
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

**Forget a manually registered dependency**

````php
register_shutdown_function(function() {
    \RivetIoc\Ioc::getInstance()->forget('App\Db');
});
````

### Locator trait

Use the RivetIoc\Traits\Locator trait in a class give access to commonly used RivetIoc\Ioc methods:

* make
* register
* forget

````php
namespace App\Services;
class UserService
{
    use \RivetIoc\Traits\Locator;
    
    protected $dao;
    
    public function __construct()
    {
        $this->dao = $this->make('App\Doa\UserDao');
    }
}
````

## How to Contribute

### Pull Requests

1. Fork the RivetIoc repository
2. Create a new branch for each feature or improvement
3. Send a pull request from each feature branch to the **develop** branch
