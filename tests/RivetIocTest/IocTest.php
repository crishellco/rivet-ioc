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

namespace RivetIocTest;

use RivetIoc\Traits\Locator,
    RivetIocTest\Classes\TestDependency,
    RivetIocTest\Classes\TestClassManual;

class IocTest extends \PHPUnit_Framework_TestCase
{

    use Locator;

    /**
     * @var string
     */
    protected $testClassAlias = 'RivetIocTest\Classes\TestClass';

    /**
     * @var string
     */
    protected $testClassSingletonAlias = 'RivetIocTest\Classes\TestClassSingleton';

    /**
     * @var string
     */
    protected $testClassManualAlias = 'RivetIocTest\Classes\TestClassManual';

    /**
     * @var string
     */
    protected $testDepedencyAlias = 'RivetIocTest\Classes\TestDependency';

    /**
     * Sets up test
     */
    protected function setUp()
    {
        // Manually register dependency
        self::register(
            $this->testClassManualAlias,
            function() {
                $dependency = new TestDependency();
                $object = new TestClassManual($dependency);

                return $object;
            }
        );
    }

    /**
     * Tears down test
     */
    protected function tearDown()
    {
        // Unregister manually registered dependency
        self::unregister($this->testClassManualAlias);
    }

    /**
     * Tests manual registration
     */
    public function testManualRegistration()
    {
        $object = self::make($this->testClassManualAlias);

        // Test that object was successfully made
        $this->assertInstanceOf(
            $this->testClassManualAlias,
            $object
        );

        // Test that the dependency was successfully injected
        $this->assertInstanceOf(
            $this->testDepedencyAlias,
            $object->getDependency()
        );
    }

    /**
     * Tests auto wiring
     */
    public function testAutoWiring()
    {
        $object = self::make($this->testClassAlias);

        // Test that object was successfully made
        $this->assertInstanceOf(
            $this->testClassAlias,
            $object
        );

        // Test that the dependency was successfully injected
        $this->assertInstanceOf(
            $this->testDepedencyAlias,
            $object->getDependency()
        );

    }

    /**
     * Tests locating singleton
     */
    public function testSingleton()
    {
        $object = self::make($this->testClassSingletonAlias);

        // Test that object was successfully made
        $this->assertInstanceOf(
            $this->testClassSingletonAlias,
            $object
        );
    }

}