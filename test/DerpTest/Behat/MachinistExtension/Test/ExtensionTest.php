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
 
namespace DerpTest\Behat\MachinistExtension\Test;

use DerpTest\Behat\MachinistExtension\Extension;
use Phake;

class ExtensionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     * @Mock
     */
    private $container;

    /**
     * @var \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface
     * @Mock
     */
    private $parameterBag;

    /**
     * @var Extension
     */
    private $extension;

    protected function setUp()
    {
        Phake::initAnnotations($this);
        Phake::when($this->container)
            ->getParameterBag()
            ->thenReturn($this->parameterBag);

        $this->extension = new Extension();
    }

    protected function tearDown()
    {
        $this->extension = null;
    }

    public function testGetCompilerPassReturnsEmptyArray()
    {
        $actual = $this->extension->getCompilerPasses();
        $this->assertInternalType('array', $actual);
        $this->assertEmpty($actual);
    }

    public function testLoadSetsParameters()
    {
        $expected = array('my config' => 'value');
        $actual = null;

        $this->extension->load($expected, $this->container);
        Phake::verify($this->container)->set('derptest.phpmachinist.behat.parameters', Phake::capture($actual));
        $this->assertEquals($expected, $actual, 'Unexpected configuration passed to container');
    }

    public function testLoadCreatesMachinistService()
    {
        $this->extension->load(array(), $this->container);
        Phake::verify($this->container)->setDefinition(
            'derptest.phpmachinist',
            $this->isInstanceOf('\Symfony\Component\DependencyInjection\Definition')
        );
    }

    public function testLoadCreatesTaggedClassGuesserService()
    {
        $this->extension->load(array(), $this->container);

        $definition = null;
        Phake::verify($this->container)->setDefinition(
            'derptest.phpmachinist.behat.context.class_guesser',
            Phake::capture($definition)
        );
        $this->assertInstanceOf('\Symfony\Component\DependencyInjection\Definition', $definition);
        $this->assertTrue($definition->hasTag('behat.context.class_guesser'));
    }

    public function testLoadCreatesTaggedInitializerService()
    {
        $this->extension->load(array(), $this->container);

        $definition = null;
        Phake::verify($this->container)->setDefinition(
            'derptest.phpmachinist.context.behat.initializer',
            Phake::capture($definition)
        );
        $this->assertInstanceOf('\Symfony\Component\DependencyInjection\Definition', $definition);
        $this->assertTrue($definition->hasTag('behat.context.initializer'));
    }
}
