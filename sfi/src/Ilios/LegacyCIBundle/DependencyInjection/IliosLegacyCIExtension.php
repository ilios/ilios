<?php

namespace Ilios\LegacyCIBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IliosLegacyCIExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        if (!isset($config['session']['encryption_key'])) {
            throw new \InvalidArgumentException(
                'The "encryption_key" option must be set for ilios_legacy_ci sessions'
            );
        }
        $container->setParameter(
            'ilios_legacy.session.cookie_name',
            $config['session']['cookie_name']
        );
        $container->setParameter(
            'ilios_legacy.session.encryption_key',
            $config['session']['encryption_key']
        );
        $container->setParameter(
            'ilios_legacy.session.encrypt_cookie',
            $config['session']['encrypt_cookie']
        );
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
