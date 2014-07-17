<?php

namespace Ilios\LegacyCIBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 * 
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ilios_legacy_ci');
        $rootNode
            ->children()
                ->arrayNode('session')->isRequired()->children()
                    ->scalarNode('cookie_name')->defaultValue('ci_session')->end()
                    ->booleanNode('encrypt_cookie')->defaultValue(false)->end()
                    ->scalarNode('encryption_key')->isRequired()->end()
                ->end();
        return $treeBuilder;
    }
}
