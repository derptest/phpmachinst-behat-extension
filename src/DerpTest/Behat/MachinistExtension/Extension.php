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

namespace DerpTest\Behat\MachinistExtension;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Adam L. Englander <adam.l.englander@coupla.co>
 *
 * Machinist extension for Behat
 */
class Extension implements \Behat\Behat\Extension\ExtensionInterface
{

    /**
     * Loads a specific configuration.
     *
     * @param array $config    Extension configuration hash (from behat.yml)
     * @param ContainerBuilder $container ContainerBuilder instance
     */
    public function load(array $config, ContainerBuilder $container)
    {
        if (!array_key_exists('store', $config)) {
            throw new InvalidConfigurationException(
                'The DerpTest\Behat\MachinistExtension\Extension requires at least one "store" to be configured'
            );
        }
    }

    /**
     * Setups configuration for current extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('truncate_on_wipe')
                    ->defaultFalse()
                ->end()
                ->arrayNode('store')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->enumNode('type')
                                ->isRequired()
                                ->values(array(
                                    'sqlite',
                                    'mysql',
                                    'mongo',
                                    'doctrine-orm',
                                    'doctrine-mongo'
                                ))
                            ->end()
                            ->scalarNode('dsn')
                                ->isRequired()
                            ->end()
                            ->scalarNode('user')
                            ->end()
                            ->scalarNode('password')
                            ->end()
                            ->scalarNode('database')
                            ->end()
                            ->arrayNode('options')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * Returns compiler passes used by this extension.
     *
     * @return array
     */
    public function getCompilerPasses()
    {
        return array();
    }
}
