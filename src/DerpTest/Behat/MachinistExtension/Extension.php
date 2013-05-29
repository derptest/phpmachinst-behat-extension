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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

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
        $this->validateConfig($config);
        $this->processDefaults($config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/services'));
        $loader->load('core.xml');
        $container->setParameter('derptest.phpmachinist.behat.parameters', $config);
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
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->enumNode('type')
                                ->isRequired()
                                ->values(array(
                                    'sqlite',
                                    'mysql',
                                    'mongo'
                                ))
                            ->end()
                            ->scalarNode('dsn')
                                ->isRequired()
                                ->cannotBeEmpty()
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
                ->arrayNode('blueprint')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('store')
                            ->defaultValue('default')
                        ->end()
                        ->scalarNode('entity')
                        ->end()
                        ->arrayNode('defaults')
                            ->addDefaultsIfNotSet()
                        ->end()
                        ->arrayNode('relationships')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                            ->children()
                                ->scalarNode('foreign')
                                    ->defaultValue('id')
                                ->end()
                                ->scalarNode('local')
                                ->end()
                            ->end()
                        ->end()
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

    protected function validateConfig(array $config)
    {
        if (isset($config['store'])) {
            $this->validateMongoStoreConfigs($config['store']);
        }
    }

    protected function validateMongoStoreConfigs(array $configs)
    {
        foreach ($configs as $config) {
            if ($config['type'] == 'mongo') {
                if (empty($config['database'])) {
                    throw new InvalidConfigurationException(
                        'The "database" configuration property must be set for all mongo stores'
                    );
                }
            }
        }
    }

    protected function processDefaults(array &$configs)
    {
        if (!empty($configs['blueprint'])) {
            foreach ($configs['blueprint'] as $key => &$blueprint) {
                if (!empty($blueprint['relationships'])) {
                    foreach ($blueprint['relationships'] as $name => &$relationship) {
                        if (empty($relationship['foreign'])) {
                            $relationship['foreign'] = 'id';
                        }
                        if (empty($relationship['local'])) {
                            $relationship['local'] = $name . 'Id';
                        }
                    }
                }

                if (empty($blueprint['entity'])) {
                    $blueprint['entity'] = $key;
                }

                if (empty($blueprint['store'])) {
                    $blueprint['store'] = 'default';
                }
            }
        }

        if (!empty($configs['store'])) {
            foreach ($configs['store'] as $name => &$store) {
                if (empty($store['entity'])) {
                    $store['entity'] = $name;
                }
            }
        }
    }
}
