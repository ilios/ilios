<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Service\EntityMetadata;
use App\Service\EntityRepositoryLookup;
use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;

class TypeResolver
{
    public function __construct(
        protected DTOInfo $dtoInfo,
        protected EntityMetadata $entityMetadata,
        protected EntityRepositoryLookup $entityRepositoryLookup,
        protected DeferredBuffer $buffer,
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
            $this->buffer->bufferRequest($type, $source->{$fieldName});
            return new Deferred(fn() => $this->buffer->getValuesForType($type, $source->{$fieldName}));
        }

        return $repository->findDTOsBy([]);
    }
}
