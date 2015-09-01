<?php
namespace Ilios\AuthenticationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration for the AuthenticationBundle
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ilios_authentication');

        $rootNode
            ->children()
                ->enumNode('type')
                    ->isRequired()
                    ->values(array('form', 'shibboleth'))
                ->end()
                ->scalarNode('legacy_salt')->defaultValue('')->end()
            ->end();

        return $treeBuilder;
    }
}
