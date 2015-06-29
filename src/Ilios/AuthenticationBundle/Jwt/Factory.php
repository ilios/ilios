<?php
namespace Ilios\AuthenticationBundle\Jwt;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class Factory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'jwt.provider.'.$id;
        $container->setDefinition(
            $providerId,
            new DefinitionDecorator('ilios_authentication.jwt.provider')
        )->replaceArgument(0, new Reference($userProvider));

        $listenerId = 'jwt.listener.'.$id;
        $container->setDefinition(
            $listenerId,
            new DefinitionDecorator('ilios_authentication.jwt.listener')
        );

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'jwt';
    }

    public function addConfiguration(NodeDefinition $node)
    {
    }
}
