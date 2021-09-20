<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\RelationshipVoter\AbstractVoter;
use App\Service\EntityMetadata;
use App\Service\EntityRepositoryLookup;
use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use ReflectionClass;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use function array_filter;
use function call_user_func;

class TypeResolver
{
    public function __construct(
        protected DTOInfo $dtoInfo,
        protected EntityMetadata $entityMetadata,
        protected EntityRepositoryLookup $entityRepositoryLookup,
        protected DeferredBuffer $buffer,
        protected AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function __invoke($source, $args, $context, ResolveInfo $info)
    {
        $fieldName = $info->fieldName;
        $ref = $this->getRef($fieldName, $source);
        $type = $this->entityMetadata->extractType($ref);
        $repository = $this->entityRepositoryLookup->getRepositoryForEndpoint($type);
        if ($source) {
            //we have already fetched an object and just need to fetch
            //things related to it
            $ids = $source->{$fieldName};
            $this->buffer->bufferRequest($type, $ids);
            $filter = [$this, 'filterValues'];
            $buffer = $this->buffer;
            return new Deferred(function () use ($buffer, $filter, $type, $ids) {
                $values = $buffer->getValuesForType($type, $ids);
                return call_user_func($filter, $values);
            });
        }

        return $this->filterValues($repository->findDTOsBy([]));
    }

    protected function getRef(string $fieldName, ?object $source): ReflectionClass
    {
        if ($source) {
            $ref = new ReflectionClass($source::class);
            $related = $this->entityMetadata->extractRelated($ref);
            return $this->dtoInfo->getRefForType($related[$fieldName]);
        } else {
            return $this->dtoInfo->getRefForType($fieldName);
        }
    }

    protected function filterValues(array $values): array
    {
        return array_filter(
            $values,
            fn($value) => $this->authorizationChecker->isGranted(AbstractVoter::VIEW, $value)
        );
    }
}
