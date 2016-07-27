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
                    ->values(array('form', 'shibboleth', 'ldap', 'cas'))
                ->end()
                ->scalarNode('legacy_salt')->defaultValue(null)->end()
                ->scalarNode('ldap_authentication_host')->defaultValue(null)->end()
                ->scalarNode('ldap_authentication_port')->defaultValue(null)->end()
                ->scalarNode('ldap_authentication_bind_template')->defaultValue(null)->end()
                ->scalarNode('shibboleth_authentication_login_path')->defaultValue(null)->end()
                ->scalarNode('shibboleth_authentication_logout_path')->defaultValue(null)->end()
                ->scalarNode('shibboleth_authentication_user_id_attribute')->defaultValue(null)->end()
                ->scalarNode('cas_authentication_server')->defaultValue(null)->end()
                ->enumNode('cas_authentication_version')
                    ->values(array(null, 1, 2, 3))
                ->end()
                ->booleanNode('cas_authentication_verify_ssl')->defaultValue(true)->end()
                ->scalarNode('cas_authentication_certificate_path')
                    ->defaultValue(null)
                    ->validate()
                    ->ifTrue(function ($value) {
                        return !is_null($value) && !\is_readable($value);
                    })->thenInvalid('Unable to find certificate at %s or it is not readable')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
