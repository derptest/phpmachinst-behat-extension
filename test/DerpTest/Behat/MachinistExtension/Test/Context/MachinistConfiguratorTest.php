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
use Phake;

/**
 * @author Adam L. Englander <adam.l.englander@coupla.co>
 */
class MachinistConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MachinistConfigurator
     */
    private $factory;

    /**
     * @var \DerpTest\Machinist\Machinist;
     * @Mock
     */
    private $machinist;

    protected function setUp()
    {
        Phake::initAnnotations($this);

        $config = array(
            'store' => array(
                'mongo-store' => array(
                    'type' => 'mongo',
                    'dsn'  => 'mongo-dsn',
                    'options' => array(
                        'mongo-option-key' => 'mongo-option-value'
                    )
                ),
                'mysql-store' => array(
                    'type' => 'mysql',
                    'dsn'  => 'mysql-dsn',
                    'options' => array(
                        'mysql-option-key' => 'mysql-option-value'
                    )
                ),
                'sqlite-store' => array(
                    'type' => 'sqlite',
                    'dsn'  => 'sqlite-dsn',
                    'options' => array(
                        'sqlite-option-key' => 'sqlite-option-value'
                    )
                )
            ),
            'blueprint' => array(
                'no-default-config' => array(
                    'store' => 'mongo-store',
                    'entity' => 'entity-x',
                    'defaults' => array(
                        'default-key' => 'default-value'
                    ),
                ),
                'default-config' => array(),
                'relationship-test' => array(
                    'relationships' => array(
                        'no-default-config' => array(
                            'foreign' => 'foreign-id',
                            'local' => 'local-id'
                        ),
                        'default-config' => array()
                    )
                )
            )
        );
        $this->factory = new MachinistConfigurator($this->machinist, $config);
    }
}
