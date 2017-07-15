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
        $container->setParameter(
            'ilios_authentication.shibboleth.login_path',
            $config['shibboleth_authentication_login_path']
        );
        $container->setParameter(
            'ilios_authentication.shibboleth.logout_path',
            $config['shibboleth_authentication_logout_path']
        );
        $container->setParameter(
            'ilios_authentication.shibboleth.user_id_attribute',
            $config['shibboleth_authentication_user_id_attribute']
        );
        $container->setParameter('ilios_authentication.legacy_salt', $config['legacy_salt']);
        $container->setParameter('ilios_authentication.type', $config['type']);
        $container->setParameter('ilios_authentication.ldap.host', $config['ldap_authentication_host']);
        $container->setParameter('ilios_authentication.ldap.port', $config['ldap_authentication_port']);
        $container->setParameter(
            'ilios_authentication.ldap.bind_template',
            $config['ldap_authentication_bind_template']
        );
        $container->setParameter('ilios_authentication.cas.server', rtrim($config['cas_authentication_server'], '/'));
        $container->setParameter('ilios_authentication.cas.version', $config['cas_authentication_version']);
        $container->setParameter('ilios_authentication.cas.verifySSL', $config['cas_authentication_verify_ssl']);
        $container->setParameter(
            'ilios_authentication.cas.certificatePath',
            $config['cas_authentication_certificate_path']
        );


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        switch ($config['type']) {
            case 'form':
                $container->setParameter(
                    'ilios_authentication.authenticatorservice',
                    'Ilios\AuthenticationBundle\Service\FormAuthentication'
                );
                break;
            case 'shibboleth':
                $container->setParameter(
                    'ilios_authentication.authenticatorservice',
                    'Ilios\AuthenticationBundle\Service\ShibbolethAuthentication'
                );
                break;
            case 'ldap':
                $container->setParameter(
                    'ilios_authentication.authenticatorservice',
                    'Ilios\AuthenticationBundle\Service\LdapAuthentication'
                );
                break;
            case 'cas':
                $container->setParameter(
                    'ilios_authentication.authenticatorservice',
                    'Ilios\AuthenticationBundle\Service\CasAuthentication'
                );
                break;
        }
    }
}
