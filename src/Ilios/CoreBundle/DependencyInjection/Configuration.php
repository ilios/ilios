<?php

namespace Ilios\CoreBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('ilios_core');

        $rootNode
            ->children()
                ->scalarNode('file_system_storage_path')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->info(
                        'An absolute path on the server where Ilios ' .
                        'should store permanent data like learning materials'
                    )
                ->end()
                ->scalarNode('ldap_directory_url')->end()
                ->scalarNode('ldap_directory_user')->end()
                ->scalarNode('ldap_directory_password')->end()
                ->scalarNode('ldap_directory_search_base')->end()
                ->scalarNode('ldap_directory_campus_id_property')->end()
                ->scalarNode('ldap_directory_username_property')->end()
                ->scalarNode('institution_domain')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->info(
                        'Internet domain name of this institution, ' .
                        'used for curriculum inventory reporting to the AAMC.'
                    )
                ->end()
                ->scalarNode('supporting_link')
                    ->defaultValue('')
                    ->info(
                        "Optional 'supporting link' for the curriculum inventory exports."
                    )
                ->end()
                ->scalarNode('frontend_timezone')
            ->end();

        return $treeBuilder;
    }
}
