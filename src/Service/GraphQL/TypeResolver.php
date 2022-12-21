<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\RelationshipVoter\AbstractVoter;
use App\Service\EntityMetadata;
use App\Service\EntityRepositoryLookup;
use App\Service\InflectorFactory;
use Doctrine\Inflector\Inflector;
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
            $value = $source->$fieldName;
            if (is_array($value)) {// one-to-many and many-to-many relationships are arrays
                $this->buffer->bufferRequest($type, $value);
                return new Deferred(
                    fn() => call_user_func([$this, 'filterValues'], $this->buffer->getValuesForType($type, $value))
                );
            } else {// one-to-one and many-to-one relationships are a single value
                $this->buffer->bufferRequest($type, [$value]);
                return new Deferred(
                    fn() => call_user_func([$this, 'authorizeValue'], $this->buffer->getValueForType($type, $value))
                );
            }
        }

        //we can pass $ars directly because our GraphQL library will reject
        //any args that aren't part of our schema
        return $this->filterValues($repository->findDTOsBy($args));
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

    protected function authorizeValue(object $value): ?object
    {
        if ($this->authorizationChecker->isGranted(AbstractVoter::VIEW, $value)) {
            return $value;
        }

        return null;
    }

    protected function filterValues(array $values): array
    {
        return array_filter(
            $values,
            fn($value) => $this->authorizationChecker->isGranted(AbstractVoter::VIEW, $value)
        );
    }
}
