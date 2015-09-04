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

namespace RivetIoc\Contracts;

/**
 * Singleton class
 *
 * This is a single class
 * to be extended
 *
 * @package    rivet-ioc
 * @author     Christopher Mitchell
 */
class Singleton {
    
    /**
     * Class instance cache
     * @var array
     */
    protected static $instances = array();

    final private function __clone() { } 
    final private function __construct() { } 
    final private function __wakeup() { }

    /**
     * Gets class instance
     * @return mixed
     */
    final public static function getInstance()
    {
        $calledClass = get_called_class();
        
        if (!isset(static::$instances[$calledClass])) {
            static::$instances[$calledClass] = new $calledClass();
        }

        return static::$instances[$calledClass];
    }
}
