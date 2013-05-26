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
use DerpTest\Machinist\Machinist;
use DerpTest\Machinist\Store\Doctrine;
use DerpTest\Machinist\Store\MongoDB;
use DerpTest\Machinist\Store\Mysql;
use DerpTest\Machinist\Store\Sqlite;
use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @author Adam L. Englander <adam.l.englander@coupla.co>
 *
 * Machinist context for implementing Machinist machines in Behat
 */
class RawMachinistContext implements ExtendedContextInterface, MachinistAwareInterface
{
    /**
     * @var ExtendedContextInterface
     */
    protected $parentContext;

    /**
     * @var \DerpTest\Machinist\Machinist
     */
    protected $machinist;

    /**
     * @var bool
     */
    protected $truncateOnWipe = false;

    /**
     * Returns main context.
     *
     * @return \Behat\Behat\Context\ExtendedContextInterface
     */
    public function getMainContext()
    {
        if ($this->parentContext) {
            $mainContext = $this->parentContext->getMainContext();
        } else {
            $mainContext = $this;
        }
        return $mainContext;
    }

    /**
     * Sets parent context of current context.
     *
     * @param \Behat\Behat\Context\ExtendedContextInterface $parentContext
    \     */
    public function setParentContext(ExtendedContextInterface $parentContext)
    {
        $this->parentContext = $parentContext;
    }

    /**
     * Find current context's sub-context by alias name.
     *
     * @param string $alias
     *
     * @return ExtendedContextInterface
     */
    public function getSubcontext($alias)
    {
        return null;
    }

    /**
     * Returns all added sub-contexts.
     *
     * @return array
     */
    public function getSubcontexts()
    {
        return array();
    }

    /**
     * Finds sub-context by it's name.
     *
     * @param string $className
     *
     * @return ContextInterface
     */
    public function getSubcontextByClassName($className)
    {
        return null;
    }

    protected function initializeStores($storeDefinitions)
    {
        foreach ($storeDefinitions as $name => $storeDefinition) {
            if ($storeDefinition['type'] == 'sqlite') {
                $store = new Sqlite(new \PDO($storeDefinition['dsn']));
            } elseif ($storeDefinition['type'] == 'mysql') {
                $store = new Mysql(new \PDO(
                    $storeDefinition['dsn'],
                    $storeDefinition['user'],
                    $storeDefinition['password'],
                    $storeDefinition['options']
                ));
            } elseif ($storeDefinition['type'] == 'mongo') {
                if (class_exists('\MongoClient')) {
                    $class = '\MongoClient';
                } else {
                    $class = '\Mongo';
                }
                if (!empty($storeDefinition['user'])) {
                    $storeDefinition['options']['username'] = $storeDefinition['user'];
                }
                if (!empty($storeDefinition['password'])) {
                    $storeDefinition['options']['password'] = $storeDefinition['password'];
                }
                $mongoClient = new $class(
                    $storeDefinition['dsn'],
                    $storeDefinition['options']
                );
                $db = $mongoClient->selectDB($storeDefinition['database']);
                $store = new MongoDB($db);
            } elseif ($storeDefinition['type'] == 'doctrine-orm') {
                if (!($this instanceof \Behat\Symfony2Extension\Context\KernelAwareInterface)) {
                    throw new InvalidDefinitionException(
                        'The doctrine-orm store type requires the Symfony2 Extension be enable'
                    );
                }

                $entityManager = $this->getContainerService($storeDefinition['dsn']);
                $store = new Doctrine($entityManager);
            } elseif ($storeDefinition['type'] == 'doctrine-mongo') {
                if (!($this instanceof \Behat\Symfony2Extension\Context\KernelAwareInterface)) {
                    throw new InvalidDefinitionException(
                        'The doctrine-orm store type requires the Symfony2 Extension be enable'
                    );
                }
                $objectManager = $this->getContainerService($storeDefinition['dsn']);
                throw new RuntimeException('doctrine-mongo is not yet implemented');
            }
            $this->getMachinist()->addStore($store, $name);
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
        if (array_key_exists('store', $parameters)) {
            $this->initializeStores($parameters['store']);
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

    private function getSymfonyContext()
    {
        $symfonyContext = $this
            ->getMainContext()
            ->getSubcontextByClassName(
                'Behat\Symfony2Extension\Extension'
            );
        return $symfonyContext;
    }
}
