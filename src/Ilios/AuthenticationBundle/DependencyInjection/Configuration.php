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
                    ->values(array('form', 'shibboleth', 'ldap'))
                ->end()
                ->scalarNode('legacy_salt')->defaultValue(null)->end()
                ->scalarNode('ldap_authentication_host')->defaultValue(null)->end()
                ->scalarNode('ldap_authentication_port')->defaultValue(null)->end()
                ->scalarNode('ldap_authentication_bind_template')->defaultValue(null)->end()
                ->scalarNode('shibboleth_authentication_login_path')->defaultValue(null)->end()
                ->scalarNode('shibboleth_authentication_logout_path')->defaultValue(null)->end()
                ->scalarNode('shibboleth_authentication_user_id_attribute')->defaultValue(null)->end()
            ->end();

        return $treeBuilder;
    }
}
