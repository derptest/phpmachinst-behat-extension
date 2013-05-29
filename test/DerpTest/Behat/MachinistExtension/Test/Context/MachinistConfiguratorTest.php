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

namespace DerpTest\Behat\MachinistExtension\Test\Context;

use DerpTest\Behat\MachinistExtension\Context\MachinistConfigurator;
use DerpTest\Machinist\Relationship;
use Phake;

/**
 * @author Adam L. Englander <adam.l.englander@coupla.co>
 */
class MachinistConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MachinistConfigurator
     */
    private $configurator;

    /**
     * @var \DerpTest\Machinist\Machinist
     * @Mock
     */
    private $machinist;

    /**
     * @var \DerpTest\Machinist\Blueprint
     * @Mock
     */
    private $blueprint1;

    /**
     * @var array
     */
    private $config;

    protected function setUp()
    {
        Phake::initAnnotations($this);

        Phake::when($this->machinist)
            ->getBlueprint('blueprint1')
            ->thenReturn($this->blueprint1);


        $this->config = array(
            'store' => array(
                'mongo-store' => array(
                    'type' => 'mongo',
                    'dsn'  => 'mongodb://localhost',
                    'database' => 'db',
                    'options' => array(
                        'w' => '1'
                    )
                ),
                'mysql-store' => array(
                    'type' => 'mysql',
                    'dsn'  => 'mysql:localhost',
                    'options' => array(
                        '1002' => 'SET NAMES utf8'
                    )
                ),
                'sqlite-store' => array(
                    'type' => 'sqlite',
                    'dsn'  => 'sqlite::memory:',
                    'options' => array(
                        '3' => '1'
                    )
                )
            ),
            'blueprint' => array(
                'blueprint1' => array(
                    'store' => 'store-1',
                    'entity' => 'entity-1',
                    'defaults' => array(
                        'default-key' => 'default-value'
                    ),
                ),
                'blueprint2' => array(
                    'store' => 'store-2',
                    'entity' => 'entity-2',
                    'defaults' => array(
                        'default-key-2' => 'default-value-2'
                    ),
                    'relationships' => array(
                        'blueprint1' => array(
                            'foreign' => 'foreign-id',
                            'local' => 'local-id'
                        )
                    )
                )
            )
        );

        $this->configurator = new MachinistConfigurator($this->machinist);
    }

    public function testBlueprintConfiguredWithProperTable()
    {
        $this->configurator->configure($this->config);

        $blueprint = null;
        Phake::verify($this->machinist)->addBlueprint(
            'blueprint2',
            Phake::capture($blueprint)
        );

        $this->assertEquals(
            $this->config['blueprint']['blueprint2']['entity'],
            $blueprint->getTable());
    }

    public function testBlueprintConfiguredWithProperMachinist()
    {
        $this->configurator->configure($this->config);

        $blueprint = null;
        Phake::verify($this->machinist)->addBlueprint(
            'blueprint2',
            Phake::capture($blueprint)
        );

        $this->assertAttributeEquals(
            $this->machinist,
            'machinist',
            $blueprint
        );
    }

    public function testBlueprintConfiguredWithProperStore()
    {
        $this->configurator->configure($this->config);

        $blueprint = null;
        Phake::verify($this->machinist)->addBlueprint(
            'blueprint2',
            Phake::capture($blueprint)
        );

        $this->assertAttributeEquals(
            $this->config['blueprint']['blueprint2']['store'],
            'store',
            $blueprint
        );
    }

    public function testBlueprintConfiguredWithProperDefaults()
    {
        $this->configurator->configure($this->config);

        $blueprint = null;
        Phake::verify($this->machinist)->addBlueprint(
            'blueprint2',
            Phake::capture($blueprint)
        );

        $expected = $this->config['blueprint']['blueprint2']['defaults'];
        $blueprint1 = new Relationship($this->blueprint1);
        $blueprint1->foreign('foreign-id');
        $blueprint1->local('local-id');
        $expected['blueprint1'] = $blueprint1;
        $this->assertAttributeEquals(
            $expected,
            'defaults',
            $blueprint
        );
    }

    public function testBlueprintConfiguredWithProperRelationship()
    {
        $this->configurator->configure($this->config);

        $blueprint = null;
        Phake::verify($this->machinist)->addBlueprint(
            'blueprint2',
            Phake::capture($blueprint)
        );

        $relationship = $blueprint->getRelationship('blueprint1');
        $this->assertInstanceOf(
            '\DerpTest\Machinist\Relationship',
            $relationship,
            'No relationship added'
        );

        $this->assertEquals(
            $this->config['blueprint']['blueprint2']['relationships']['blueprint1']['foreign'],
            $relationship->getForeign(),
            'Unexpected foreign value in relationship'
        );

        $this->assertEquals(
            $this->config['blueprint']['blueprint2']['relationships']['blueprint1']['local'],
            $relationship->getLocal(),
            'Unexpected local value in relationship'
        );

        $this->assertEquals(
            $this->blueprint1,
            $relationship->getBlueprint(),
            'Unexpected relationship blueprint'
        );
    }
}
