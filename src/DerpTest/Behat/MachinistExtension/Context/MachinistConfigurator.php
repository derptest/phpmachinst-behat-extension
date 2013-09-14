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
use DerpTest\Machinist\Blueprint;
use DerpTest\Machinist\Machinist;
use DerpTest\Machinist\Relationship;
use DerpTest\Machinist\Store\SqlStore;

/**
 * @author Adam L. Englander <adam.l.englander@coupla.co>
 *
 * Machinist factory to configure stores and blueprints
 * based on the config array
 */
class MachinistConfigurator
{
    /**
     * @var Machinist
     */
    private $machinist;

    public function __construct(Machinist $machinist)
    {
        $this->machinist = $machinist;
    }

    public function configure(array $config)
    {
        if (isset($config['store']))
        {
            $this->configureStores($config['store']);
        }

        if (isset($config['blueprint'])) {
            $this->configureBlueprints($config['blueprint']);
        }
    }

    protected function configureStores(array $config)
    {
        foreach ($config as $name => $storeConfig) {
            $this->addSqlStore($name, $storeConfig);
        }
    }

    protected function addSqlStore($name, array $config)
    {
        $username = isset($config['user'])? $config['user'] : null;
        $password = isset($config['password'])? $config['password'] : null;
        $options  = isset($config['options']) ? $config['options'] : array();
        $options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
        $pdo = new \PDO($config['dsn'], $username, $password, $options);
        $store = SqlStore::fromPdo($pdo);
        $this->machinist->addStore($store, $name);
    }

    protected function configureBlueprints(array $config)
    {
        foreach ($config as $name => $blueprintConfig) {
            $defaults = isset($blueprintConfig['defaults']) ? $blueprintConfig['defaults'] : array();
            if (isset($blueprintConfig['relationships'])) {
                $this->addRelationshipsToDefaults($blueprintConfig['relationships'], $defaults);
            }

            $blueprint = new Blueprint(
                $this->machinist,
                $blueprintConfig['entity'],
                $defaults,
                $blueprintConfig['store']
            );

            $this->machinist->addBlueprint($name, $blueprint);
        }
    }

    protected function addRelationshipsToDefaults(array $relationshipConfigs, array &$defaults)
    {
        foreach ($relationshipConfigs as $target => $relationshipConfig) {
            $relationship = new Relationship($this->machinist->getBlueprint($target));
            $relationship->local($relationshipConfig['local']);
            $relationship->foreign($relationshipConfig['foreign']);
            $defaults[$target] = $relationship;
        }
    }
}
