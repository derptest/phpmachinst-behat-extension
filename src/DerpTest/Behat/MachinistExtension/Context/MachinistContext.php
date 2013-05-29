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
use DerpTest\Machinist\Store\SqlStore;

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
     * @throws \InvalidArgumentException
     * @Given /^the following "(?P<blueprint>(?:[^"]|\\")*)" data exists:$/
     */
    public function theFollowingMachinesExist($blueprint, TableNode $table)
    {
        $arr = array();
        foreach ($table->getHash() as $row) {
            $bp = $this->getMachinist()->getBlueprint($blueprint);
            if (!$bp) {
                throw new \InvalidArgumentException(
                    sprintf('No blueprint %s found', $blueprint)
                );
            }
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
     * @Given /^I wipe all "(?P<blueprint>(?:[^"]|\\")*)" data$/
     */
    public function thereAreNoneOfTheseMachines($blueprint)
    {
        $this->getMachinist()
            ->getBlueprint($blueprint)
            ->wipe($this->truncateOnWipe);
    }

    /**
     * @Given /^I wipe all data$/
     */
    public function thereAreNoMachines()
    {
        $this->getMachinist()->wipeAll($this->truncateOnWipe);
    }

    /**
     * @Then /^there is no "(?P<blueprint>(?:[^"]|\\")*)" data$/
     */
    public function thereIsNoData($blueprint)
    {
        $rows = $this->getMachinist()
            ->getBlueprint($blueprint)
            ->count();
        if ($rows > 0) {
            throw new \RuntimeException(sprintf('%d records were found', count($rows)));
        }
    }

    /**
     * @Then /^the following "(?P<blueprint>(?:[^"]|\\")*)" data is found:$/
     */
    public function theFollowingDataIsFound($blueprint, TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $bp = $this->getMachinist()->getBlueprint($blueprint);
            if (!$bp) {
                throw new \InvalidArgumentException(
                    sprintf('No blueprint %s found', $blueprint)
                );
            }
            $search = array();
            foreach ($row as $key => $val) {
                if ($bp->hasRelationship($key)) {
                    $relOverrides = $this->findRelationalOverrides($val);
                    $relationship = $bp->getRelationship($key);
                    $search[$key] = $relationship->getBlueprint()->findOrCreate($relOverrides);
                } else {
                    $search[$key] = $val;
                }
            }
            $result = $bp->findOne($search);
            if (!$result) {
                throw new \RuntimeException('Could not find one or more data records');
            }
        }
    }


    /**
     * @Then /^only the following "(?P<blueprint>(?:[^"]|\\")*)" data is found:$/
     */
    public function theOnlyFollowingDataIsFound($blueprint, TableNode $table)
    {
        $this->theFollowingDataIsFound($blueprint, $table);
        $dataRows = $this->getMachinist()
            ->getBlueprint($blueprint)
            ->count();
        $tableRows = count($table->getHash());
        if ($tableRows > $dataRows) {
            throw new \RuntimeException(sprintf('%d more records were found than expected', $dataRows - $tableRows));
        } elseif ($tableRows < $dataRows) {
            throw new \RuntimeException(sprintf('%d fewer records were found than expected', $tableRows - $dataRows));
        }
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
}
