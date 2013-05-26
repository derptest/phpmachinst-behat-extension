<?php
/**
 * Copyright (c) 2013 Adam L. Englander
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace DerpTest\Behat\MachinistExtension\Test\Context\Initializer;

use Behat\Behat\Context\ContextInterface;
use DerpTest\Behat\MachinistExtension\Context\Initializer\MachinistAwareInitializer;
use DerpTest\Behat\MachinistExtension\Context\MachinistContext;
use DerpTest\Machinist\Machinist;
use Phake;

/**
 * @author Adam L. Englander <adam.l.englander@coupla.co>
 */
class MachinistAwareInitializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \DerpTest\Behat\MachinistExtension\Context\MachinistAwareInterface|ContextInterface
     */
    private $machinistAware;

    /**
     * @var \DerpTest\Machinist\Machinist
     * @Mock
     */
    private $machinist;

    protected  function setUp()
    {
        Phake::initAnnotations($this);
    }

    protected function tearDown()
    {
        $this->machinistAware = null;
        $this->machinist = null;
    }

    public function testSupportsReturnsTrueForImplementingObject()
    {
        $machinistAware = new MachinistContext();
        $initializer = new MachinistAwareInitializer($this->machinist, array());
        $actual = $initializer->supports($machinistAware);
        $this->assertTrue($actual);
    }

    public function testSupportsReturnsFalseForImplementingObject()
    {
        $initializer = new MachinistAwareInitializer($this->machinist, array());
        $context = Phake::mock('\Behat\Behat\Context\ContextInterface');
        $actual = $initializer->supports($context);
        $this->assertFalse($actual);
    }

    public function testInitialize()
    {
        $context = Phake::mock('\DerpTest\Behat\MachinistExtension\Context\MachinistContext');
        $expectedArray = array('Expected' => 'array');
        $initializer = new MachinistAwareInitializer($this->machinist, $expectedArray);
        $initializer->initialize($context);

        Phake::verify($context)->setMachinist($this->machinist);
        Phake::verify($context)->setParameters($expectedArray);

        $this->assertTrue(true);
    }
}
