<?php
/**
 * rivet-ioc - An auto-wiring IoC container for PHP
 *
 * @author      Christopher Mitchell
 * @copyright   2015 Chris Mitchell
 * @link        https://github.com/crishellco/rivet-ioc
 * @license     https://github.com/crishellco/rivet-ioc/blob/master/LICENSE
 * @version     1.0
 * @package     rivet-ioc
 *
 * MIT LICENSE
 *
 * Copyright (c) 2015 Christopher Mitchell
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

use RivetIoc\Contracts\Singleton,
    \ReflectionClass,
    \Exception;

/**
 * Ioc class
 *
 * A manually and recursively auto-wiring
 * IoC container for PHP.
 *
 * @package     rivet-ioc
 * @author      Christopher Mitchell
 */
Class Ioc extends Singleton {

    /**
     * @var array
     */
    private $registry = [];

    /**
     * Registers new dependency
     * This allows us to manually define how to create a new resource,
     * for instance if we want a singleton rather than a new instance.
     * @param string $alias
     * @param callable $closure
     * @return \RivetIoc\Ioc
     * @throws Exception
     */
    public function register($alias, callable $closure)
    {
        // Validate that dependency hasn't already been registered
        if($this->isRegistered($alias)) {
            throw new Exception("'$alias' has already been registered in the Ioc registry");
        }

        $this->registry[$alias] = $closure;

        return $this;
    }

    /**
     * Unregisters dependency
     * @param string $alias
     * @return \RivetIoc\Ioc
     */
    public function unregister($alias)
    {
        // Validate that dependency has been registered
        if($this->isRegistered($alias)) {
            unset($this->registry[$alias]);
        }

        return $this;
    }

    /**
     * Makes new object using a registered creation process
     * or auto-wiring
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
     * Gets if dependency creation process has been registered
     * @param string $alias
     * @return bool
     */
    protected function isRegistered($alias)
    {
        return array_key_exists($alias, $this->registry);
    }

    /**
     * Gets registered closure if exists
     * @param string $alias
     * @return callable|null
     */
    protected function getRegisteredClosure($alias)
    {
        return $this->isRegistered($alias)
            ? $this->registry[$alias]
            : null;
    }

    /**
     * Generates closure using auto-wiring and caches
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

        // Create new closure
        $newObject = $reflectionClass->newInstanceArgs($args);
        $closure = function() use ($newObject) {
            return $newObject;
        };
        
        // Cache
        $this->registry[$alias] = $closure;
        
        return $closure;
    }

    /**
     * Makes new object (or null if no type
     * hint) for each method argument
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
