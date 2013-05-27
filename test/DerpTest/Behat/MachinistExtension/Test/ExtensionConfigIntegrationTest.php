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

/**
 * @author Adam L. Englander <adam.l.englander@coupla.co>
 *
 * Integration tests for Extension configuration\
 *
 * These tests use the actual configuration classes and functions to ensure the getConfig method behaves as expected
 */

namespace DerpTest\Behat\MachinistExtension\Test;


use DerpTest\Behat\MachinistExtension\Extension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;

class ExtensionConfigIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeInterface
     */
    private $node;

    /**
     * @var array Minimum config to avoid error
     */
    private $requiredConfig = array(
        'store' => array(
            'default' => array(
                'type' => 'doctrine-orm',
            )
        )
    );


    protected function setUp()
    {
        $extension = new Extension();
        $definition = new ArrayNodeDefinition('test');
        $extension->getConfig($definition);
        $this->node = $definition->getNode();
    }

    protected function tearDown()
    {
        $this->node = null;
    }

    public function testTruncateOnWipeCanBeConfigured()
    {
        $actual = $this->node->finalize($this->requiredConfig);
        $this->assertArrayHasKey('truncate_on_wipe', $actual);
    }

    public function testTruncateOnWipeDefaultsToFalse()
    {
        $actual = $this->node->finalize($this->requiredConfig);
        $this->assertFalse($actual['truncate_on_wipe']);
    }

    public function testTruncateOnWipeCanUseConfig()
    {
        $actual = $this->node->finalize(
            array_merge(
                $this->requiredConfig,
                array('truncate_on_wipe' => true)
            )
        );
        $this->assertTrue($actual['truncate_on_wipe']);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testStoreSettingsDefault()
    {
        $this->node->finalize(array());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testStoreSettingsMissingTypeErrors()
    {
        $this->node->finalize(
            array(
                'store' => array(
                    'default' => array(
                    )
                )
            )
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testStoreInvalidTypeErrors()
    {
        $this->node->finalize(
            array(
                'store' => array(
                    'default' => array(
                        'type' => 'invalid',
                    )
                )
            )
        );
    }

    /**
     * @dataProvider validStoreTypeValueProvider
     */
    public function testStoreValidTypesReturnValidValues()
    {
        $expected = array(
            'default' => array(
                'type' => 'sqlite',
            )
        );
        $actual = $this->node->finalize(array('store' => $expected));

        $this->assertInternalType('array', $actual);
        $this->assertArrayHasKey('store', $actual);
        $this->assertEquals($expected, $actual['store']);
    }

    public function validStoreTypeValueProvider()
    {
        return array(
            'SQLite' => array('sqlite'),
            'MySql' => array('mysql'),
            'Mongo DB' => array('mongo'),
            'Doctrine ORM' => array('doctrine-orm'),
            'Doctrine MongoDB' => array('doctrine-mongo'),
        );
    }
}
