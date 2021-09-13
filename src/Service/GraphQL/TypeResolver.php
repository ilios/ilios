<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Service\EntityMetadata;
use App\Service\EntityRepositoryLookup;
use GraphQL\Type\Definition\ResolveInfo;

class TypeResolver
{
    public function __construct(
        protected DTOInfo $dtoInfo,
        protected EntityMetadata $entityMetadata,
        protected EntityRepositoryLookup $entityRepositoryLookup,
    ) {
    }

    public function __invoke($source, $args, $context, ResolveInfo $info)
    {
        $fieldName = $info->fieldName;
        $ref = $this->dtoInfo->getRefForType($fieldName);
        $type = $this->entityMetadata->extractType($ref);
        $repository = $this->entityRepositoryLookup->getRepositoryForEndpoint($type);
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
}
