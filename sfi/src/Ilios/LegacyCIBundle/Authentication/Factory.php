<?php

namespace Ilios\LegacyCIBundle\Authentication;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class Factory implements SecurityFactoryInterface
{

    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.ilios_legacy_ci.' . $id;
        $container->setDefinition(
            $providerId,
            new DefinitionDecorator('ilios_legacy_ci.security.authentication.provider')
        )
        ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'security.authentication.listener.ilios_legacy_ci.' . $id;
        $container->setDefinition(
            $listenerId,
            new DefinitionDecorator('ilios_legacy_ci.security.authentication.listener')
        );

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'ilios_legacy_ci';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        
    }
}
