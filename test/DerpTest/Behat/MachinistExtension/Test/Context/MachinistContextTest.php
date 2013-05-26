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

use Phake;
use DerpTest\Behat\MachinistExtension\Context\MachinistContext;

class MachinistContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MachinistContext
     */
    private $context;

    /**
     * @var \DerpTest\Machinist\Machinist
     */
    private $machinist;

    protected function setUp()
    {
        $this->machinist = Phake::mock('\DerpTest\Machinist\Machinist');
        $this->context = new MachinistContext();
        $this->context->setMachinist($this->machinist);
    }

    protected function tearDown()
    {
        $this->machinist = null;
        $this->context = null;
    }

    public function testThereAreNoneOfTheseMachines()
    {
        $blueprint = Phake::mock('\DerpTest\Machinist\Blueprint');
        Phake::when($this->machinist)
            ->getBlueprint(Phake::anyParameters())
            ->thenReturn($blueprint);
        $blueprintName = 'Blueprint Name';
        $this->context->thereAreNoneOfTheseMachines($blueprintName);

        Phake::verify($this->machinist)->getBlueprint($blueprintName);
        Phake::verify($blueprint)->wipe(false);
    }

    public function testThereAreNoneOfTheseMachinesUsesTruncateOnWipeParameter()
    {
        $this->context->setMachinistParameters(
            array('truncate_on_wipe' => true)
        );
        $blueprint = Phake::mock('\DerpTest\Machinist\Blueprint');
        Phake::when($this->machinist)
            ->getBlueprint(Phake::anyParameters())
            ->thenReturn($blueprint);
        $blueprintName = 'Blueprint Name';
        $this->context->thereAreNoneOfTheseMachines($blueprintName);

        Phake::verify($this->machinist)->getBlueprint($blueprintName);
        Phake::verify($blueprint)->wipe(true);
    }

    public function testThereAreNoMachines()
    {
        $this->context->thereAreNoMachines();

        Phake::verify($this->machinist)->wipeAll(false);
    }

    public function testThereAreNoMachinesUsesTruncateOnWipeParameter()
    {
        $this->context->setMachinistParameters(
            array('truncate_on_wipe' => true)
        );

        $this->context->thereAreNoMachines();

        Phake::verify($this->machinist)->wipeAll(true);
    }
}
