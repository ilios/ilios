<?php

namespace Ilios\WebBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ilios_web');

        $rootNode
            ->children()
                ->enumNode('environment')
                  ->values(array('production', 'staging'))
                  ->defaultValue('production')
                ->end()
                ->scalarNode('version')->defaultValue(false)->end()
                ->scalarNode('production_bucket_path')
                  ->defaultValue('https://s3-us-west-1.amazonaws.com/iliosindex/')
                ->end()
                ->scalarNode('staging_bucket_path')
                  ->defaultValue('https://s3-us-west-1.amazonaws.com/dev-iliosindex/')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
