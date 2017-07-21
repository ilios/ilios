<?php

namespace Ilios\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IliosCoreExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ilios_core.file_store_path', $config['file_system_storage_path']);
        $container->setParameter('ilios_core.ldap.url', $config['ldap_directory_url']);
        $container->setParameter('ilios_core.ldap.user', $config['ldap_directory_user']);
        $container->setParameter('ilios_core.ldap.password', $config['ldap_directory_password']);
        $container->setParameter('ilios_core.ldap.search_base', $config['ldap_directory_search_base']);
        $container->setParameter('ilios_core.ldap.campus_id_property', $config['ldap_directory_campus_id_property']);
        $container->setParameter('ilios_core.ldap.username_property', $config['ldap_directory_username_property']);
        $container->setParameter('ilios_core.institution_domain', $config['institution_domain']);
        $container->setParameter('ilios_core.supporting_link', $config['supporting_link']);
        $container->setParameter('ilios_core.timezone', $config['timezone']);
        $container->setParameter('ilios_core.enable_tracking', $config['enable_tracking']);
        $container->setParameter('ilios_core.tracking_code', $config['tracking_code']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
