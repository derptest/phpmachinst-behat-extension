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

namespace DerpTest\Behat\MachinistExtension\Context;

use Behat\Behat\Context\ExtendedContextInterface;
use Behat\Gherkin\Node\TableNode;
use DerpTest\Machinist\Machinist;

/**
 * @author Adam L. Englander <adam.l.englander@coupla.co>
 *
 * Machinist context for implementing Machinist machines in Behat
 */
class MachinistContext extends RawMachinistContext implements MachinistAwareInterface
{
    /**
     * @param $blueprint
     * @param TableNode $table
     *
     * @Given /^the following (\w+) exists:$/
     */
    public function theFollowingMachinesExist($blueprint, TableNode $table)
    {
        $arr = array();
        foreach ($table->getHash() as $row) {
            $bp = $this->getMachinist()->getBlueprint($blueprint);
            $overrides = array();
            foreach ($row as $key => $val) {
                if ($bp->hasRelationship($key)) {
                    $relOverrides = $this->findRelationalOverrides($val);
                    $relationship = $bp->getRelationship($key);
                    $overrides[$key] = $relationship->getBlueprint()->findOrCreate($relOverrides);
                } else {
                    $overrides[$key] = $val;
                }
            }
            $arr[] = $bp->make($overrides);
        }
        $this->blueprints[$blueprint] = $arr;
    }

    /**
     * @param $blueprint
     *
     * @Given /^there are no (\w+) machines$/
     */
    public function thereAreNoneOfTheseMachines($blueprint)
    {
        $this->getMachinist()
            ->getBlueprint($blueprint)
            ->wipe($this->truncateOnWipe);
    }

    /**
     * @Given /^there are no machines$/
     */
    public function thereAreNoMachines()
    {
        $this->getMachinist()->wipeAll($this->truncateOnWipe);
    }

    private function findRelationalOverrides($valueString)
    {
        //regex is hard so lets just do this :p
        $values = array_map('trim', explode(',', $valueString));
        $relOverrides = array();
        foreach ($values as $value) {
            if (preg_match_all('/([^\s:]+)\s*:\s*(.+)/', $value, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $relOverrides[$match[1]] = $match[2];
                }
            }
        }
        return $relOverrides;
    }

    protected function initializeStores($databases)
    {
        $set_default = false;
        foreach ($databases as $name => $db) {
            if (array_key_exists('driver', $db)) {
                $store = new $db['driver']($db);
            } else {
                $user = empty($db['user']) ? 'root' : $db['user'];
                $password = empty($db['password']) ? null : $db['password'];
                $pdo = new PDO($db['dsn'], $user, $password, array());
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $store = SqlStore::fromPdo($pdo);
            }
            $this->getMachinist()->Store($store, $name);
            if ((array_key_exists('default', $db) && $db['default']) || !$set_default) {
                $set_default = true;
                $this->getMachinist()->Store($store);
            }
        }
    }

    /**
     * @return Machinist
     */
    protected function getMachinist()
    {
        return $this->machinist;
    }

    protected function processParameters(array $parameters)
    {
        if (array_key_exists('database', $parameters)) {
            $this->initializeStores($parameters['database']);
        }

        if (array_key_exists('truncate_on_wipe', $parameters)) {
            $this->truncateOnWipe = (bool) $parameters['truncate_on_wipe'];
        }
    }

    /**
     * Set Machinist
     *
     * @param Machinist $machinist
     * @return void
     */
    public function setMachinist(Machinist $machinist)
    {
        $this->machinist = $machinist;
    }

    /**
     * Set the Machinist parameters
     *
     * @param array $parameters
     * @return void
     */
    public function setMachinistParameters(array $parameters)
    {
        $this->processParameters($parameters);

    }
}
