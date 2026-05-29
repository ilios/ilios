<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Entity\DTO\LearningMaterialDTO;
use App\Service\LearningMaterialDecoratorFactory;
use GraphQL\Type\Definition\ResolveInfo;

class LearningMaterialFieldResolver
{
    public function __construct(
        protected FieldResolver $fieldResolver,
        protected LearningMaterialDecoratorFactory $learningMaterialDecorator
    ) {
    }

    public function __invoke(mixed $source, array $args, mixed $context, ResolveInfo $info): mixed
    {

        $fieldName = $info->fieldName;

        if (
            $fieldName === 'absoluteFileUri' &&
            $source instanceof LearningMaterialDTO
        ) {
            return $this->learningMaterialDecorator->getAbsoluteFileUriForDTO($source);
        }

        return $this->fieldResolver->__invoke($source, $args, $context, $info);
    }
}
