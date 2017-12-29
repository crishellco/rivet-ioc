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

namespace RivetIocTest;

use RivetIoc\Ioc;
use RivetIoc\Traits\Locator;
use RivetIocTest\Classes\TestDependency;
use RivetIocTest\Classes\TestClassManual;
use RivetIoc\Exceptions\RivetIocException;

class IocTest extends \PHPUnit_Framework_TestCase
{

    use Locator;

    /**
     * @var string
     */
    public $testClassAlias = 'RivetIocTest\Classes\TestClass';

    /**
     * @var string
     */
    public $testClassSingletonAlias = 'RivetIocTest\Classes\TestClassSingleton';

    /**
     * @var string
     */
    public static $testClassManualAlias = 'RivetIocTest\Classes\TestClassManual';

    /**
     * @var string
     */
    public $testDepedencyAlias = 'RivetIocTest\Classes\TestDependency';

    /**
     * @var string
     */
    public static $manuallyCreatedMessage = 'I was created in a manually registered closure';

    /**
     * Sets up test.
     */
    public static function setUpBeforeClass()
    {
        // Manually register handler
        $message = self::$manuallyCreatedMessage;
        Ioc::instance()->register(
            self::$testClassManualAlias,
            function() use ($message) {
                $dependency = new TestDependency;
                $object = new TestClassManual($dependency);

                // Set an object property to test later
                $object->message = $message;

                return $object;
            }
        );
    }

    /**
     * Tests manual registration.
     */
    public function test_can_manually_register()
    {
        $object = $this->make(self::$testClassManualAlias);

        // Test that object was successfully made
        $this->assertInstanceOf(
            self::$testClassManualAlias,
            $object
        );

        // Test that the dependency was successfully injected
        $this->assertInstanceOf(
            $this->testDepedencyAlias,
            $object->getDependency()
        );
        $this->assertEquals(
            self::$manuallyCreatedMessage,
            $object->message
        );
    }

    public function test_can_forget_manually_registered()
    {
        // Forgets manually registered handler
        $this->forget(self::$testClassManualAlias);

        // Creates a new object and validates that it was not made using a closure
        $object = $this->make(self::$testClassManualAlias);
        $this->assertNull($object->message);
    }

    /**
     * Tests auto wiring.
     */
    public function test_dependency_auto_wiring()
    {
        $object = $this->make($this->testClassAlias);

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
     * Tests locating singleton.
     */
    public function test_can_resolve_singleton()
    {
        $object = $this->make($this->testClassSingletonAlias);

        // Test that object was successfully made
        $this->assertInstanceOf(
            $this->testClassSingletonAlias,
            $object
        );
    }

    /**
     * Tests registering a previously registered handler.
     */
    public function test_cannot_manually_register_again()
    {
        try {
            $this->register(self::$testClassManualAlias, function() {});
        } catch(RivetIocException $e) {
            return;
        }

        $this->fail('Expected RivetIocException not thrown.');

    }

}
