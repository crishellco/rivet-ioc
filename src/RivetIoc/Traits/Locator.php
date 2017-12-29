<?php
/**
 * rivet-ioc - An auto-wiring IoC container for PHP
 *
 * @author      Christopher Mitchell
 * @copyright   2018 Christopher Mitchell
 * @link        https://github.com/crishellco/rivet-ioc
 * @license     https://github.com/crishellco/rivet-ioc/blob/master/LICENSE
 * @version     1.0
 * @package     rivet-ioc
 *
 * MIT LICENSE
 *
 * Copyright (c) 2018 Christopher Mitchell
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

namespace RivetIoc\Traits;

use RivetIoc\Ioc;

/**
 * Locator trait
 *
 * This trait gives static access to the
 * make, register, and forget methods
 * from the Ioc class
 *
 * @package RivetIoc\Traits
 * @author Christopher Mitchell
 */
trait Locator {

    /**
     * Makes new object.
     *
     * @param string $alias
     * @return object
     */
    public function make($alias)
    {
        return Ioc::instance()->make($alias);
    }

    /**
     * Registers new dependency.
     *
     * This allows us to manually define how to create a new resource,
     * for instance if we want a singleton rather than a new instance.
     *
     * @param string $alias
     * @param callable $closure
     * @return \RivetIoc\Ioc
     * @throws Exception
     */
    public function register($alias, callable $closure)
    {
        return Ioc::instance()->register($alias, $closure);
    }

    /**
     * Forgets manually registered dependency.
     *
     * @param string $alias
     * @return \RivetIoc\Ioc
     */
    public function forget($alias)
    {
        return Ioc::instance()->forget($alias);
    }

}
