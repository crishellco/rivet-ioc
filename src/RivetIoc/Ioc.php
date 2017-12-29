<?php
/**
 * rivet-ioc - An auto-wiring IoC container for PHP
 *
 * @author      Christopher Mitchell
 * @copyright   2016-2017 Christopher Mitchell
 * @link        https://github.com/crishellco/rivet-ioc
 * @license     https://github.com/crishellco/rivet-ioc/blob/master/LICENSE
 * @version     1.0
 * @package     rivet-ioc
 *
 * MIT LICENSE
 *
 * Copyright (c) 2016-2017 Christopher Mitchell
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace RivetIoc;

use RivetIoc\Exceptions\RivetIocException;
use RivetIoc\Contracts\Singleton;
use \ReflectionClass;

/**
 * Ioc class
 *
 * A manually and recursively auto-wiring
 * IoC container for PHP.
 *
 * @package RivetIoc
 * @author Christopher Mitchell
 */
Class Ioc extends Singleton {

    /**
     * @var array
     */
    private $handlers = [];

    /**
     * Registers new dependency.
     *
     * This allows us to manually define how to create a new resource,
     * for instance if we want a singleton rather than a new instance.
     *
     * @param string $alias
     * @param callable $closure
     * @return \RivetIoc\Ioc
     * @throws \RivetIoc\Exceptions\RivetIocException
     */
    public function register($alias, callable $closure)
    {
        // Validate that dependency hasn't already been registered
        if($this->isRegistered($alias)) {
            throw new RivetIocException("'$alias' has already been registered");
        }

        $this->handlers[$alias] = $closure;

        return $this;
    }

    /**
     * Forgets manually registered dependency.
     *
     * @param string $alias
     * @return \RivetIoc\Ioc
     */
    public function forget($alias)
    {
        unset($this->handlers[$alias]);

        return $this;
    }

    /**
     * Makes new object using a registered creation process.
     * or auto-wiring.
     *
     * @param string $alias
     * @return object
     */
    public function make($alias)
    {
        // Get closure to create new object
        $closure = $this->getRegisteredClosure($alias)
            ?: $this->getGeneratedClosure($alias);

        // Execute callable closure to create new object
        $object = $closure();

        return $object;
    }

    /**
     * Gets if dependency creation process has been registered.
     *
     * @param string $alias
     * @return bool
     */
    protected function isRegistered($alias)
    {
        return array_key_exists($alias, $this->handlers);
    }

    /**
     * Gets registered closure if exists.
     *
     * @param string $alias
     * @return callable|null
     */
    protected function getRegisteredClosure($alias)
    {
        return $this->isRegistered($alias)
            ? $this->handlers[$alias]
            : null;
    }

    /**
     * Generates closure using auto-wiring and caches.
     *
     * @param string $alias
     * @return callable
     */
    protected function getGeneratedClosure($alias)
    {
        // Get reflection class
        $reflectionClass = new ReflectionClass($alias);

        // Get get class constructor
        // getConstructor method returns null if no class constructor exists
        $constructor = $reflectionClass->getConstructor();

        // If constructor found, make new object(s) for
        // each parameter to pass as arguments
        $args = $constructor
            ? $this->makeArguments($constructor->getParameters())
            : array();

        // Create new object
        if($reflectionClass->isSubclassOf('RivetIoc\Contracts\Singleton')) {
            // TODO allow dependency injection with children of Singleton
            $newObject = call_user_func(
                array($reflectionClass->getName(), 'instance')
            );
        } else {
            $newObject = $reflectionClass->newInstanceArgs($args);
        }

        // Create closure that returns newly created object
        $closure = function() use ($newObject) {
            return $newObject;
        };

        // Cache
        $this->handlers[$alias] = $closure;

        return $closure;
    }

    /**
     * Makes new object (or null if no type
     * hint) for each method argument.
     *
     * @param array $reflectionParameters
     * @return array
     */
    protected function makeArguments(array $reflectionParameters)
    {
        // Create variable for each  parameter by using
        // the type hint of the parameter. If no type hint, null
        // will be passed to the method for that argument.
        $args = array();
        foreach($reflectionParameters as $reflectionParameter) {
            $class = $reflectionParameter->getClass();

            $args[] = $class
                ? $this->make($class->getName())
                : null;
        }

        return $args;
    }

}
