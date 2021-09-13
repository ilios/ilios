<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Repository\RepositoryInterface;
use App\Service\EntityMetadata;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Psr\Container\ContainerInterface;

class TypeResolver
{
    public function __construct(
        protected DTOInfo $dtoInfo,
        protected EntityMetadata $entityMetadata,
        protected ContainerInterface $container,
    ) {
    }

    public function __invoke($source, $args, $context, ResolveInfo $info)
    {
        $fieldName = $info->fieldName;
        $ref = $this->dtoInfo->getRefForType($fieldName);
        $repositoryName = $this->entityMetadata->extractRepository($ref);
        $repository = $this->getRepository($repositoryName);
        if ($source) {
            //we have already fetched an object and just need to fetch
            //things related to it
            $idField = $this->entityMetadata->extractId($ref);
            return $repository->findDTOsBy([
                "${idField}" => $source->{$fieldName}
            ]);
        }

        return $repository->findDTOsBy([]);
    }

    protected function getRepository(string $name): RepositoryInterface
    {
        if (!$this->container->has($name)) {
            throw new Exception("Unable to locate repository ${name} is it in getSubscribedServices()?");
        }

        $repository = $this->container->get($name);
        if (!($repository instanceof RepositoryInterface)) {
            throw new Exception(sprintf("%s does not implement RepositoryInterface", $repository::class));
        }

        return $repository;
    }
}
