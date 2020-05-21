<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Manager\ManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EntityManagerLookup
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EndpointResponseNamer
     */
    protected $endpointResponseNamer;

    public function __construct(ContainerInterface $container, EndpointResponseNamer $endpointResponseNamer)
    {
        $this->container = $container;
        $this->endpointResponseNamer = $endpointResponseNamer;
    }

    public function getManagerForEndpoint(string $endPoint): ManagerInterface
    {
        $entityName = $this->getEntityName($endPoint);
        $name = "App\\Entity\\Manager\\${entityName}Manager";
        if (!$this->container->has($name)) {
            throw new Exception(
                sprintf('The manager for \'%s\' does not exist. Is the serivice public?', $name)
            );
        }

        $manager = $this->container->get($name);

        if (!$manager instanceof ManagerInterface) {
            $class = $manager->getClass();
            throw new Exception("{$class} is not an Ilios Manager.");
        }

        return $manager;
    }


    /**
     * Get the Entity name for an endpoint
     *
     */
    protected function getEntityName(string $name): string
    {
        return ucfirst($this->endpointResponseNamer->getSingularName($name));
    }
}
