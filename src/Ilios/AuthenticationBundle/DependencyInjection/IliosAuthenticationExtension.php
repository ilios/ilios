<?php

namespace Ilios\AuthenticationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IliosAuthenticationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ilios_authentication.legacy_salt', $config['legacy_salt']);
        $container->setParameter('ilios_authentication.type', $config['type']);
        $container->setParameter('ilios_authentication.ldap.host', $config['ldap_authentication_host']);
        $container->setParameter('ilios_authentication.ldap.port', $config['ldap_authentication_port']);
        $container->setParameter(
            'ilios_authentication.ldap.bind_template',
            $config['ldap_authentication_bind_template']
        );
        
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('voters.yml');
        $loader->load('dto_voters.yml');

        switch ($config['type']) {
            case 'form':
                $container->setParameter(
                    'ilios_authentication.authenticatorservice',
                    'ilios_authentication.form.authentication'
                );
                break;
            case 'shibboleth':
                $container->setParameter(
                    'ilios_authentication.authenticatorservice',
                    'ilios_authentication.shibboleth.authentication'
                );
                break;
            case 'ldap':
                $container->setParameter(
                    'ilios_authentication.authenticatorservice',
                    'ilios_authentication.ldap.authentication'
                );
                break;
        }
    }
}
