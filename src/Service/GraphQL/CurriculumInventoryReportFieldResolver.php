<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Entity\DTO\CurriculumInventoryReportDTO;
use App\Service\CurriculumInventoryReportDecoratorFactory;
use GraphQL\Type\Definition\ResolveInfo;

class CurriculumInventoryReportFieldResolver
{
    public function __construct(
        protected FieldResolver $fieldResolver,
        protected CurriculumInventoryReportDecoratorFactory $decorator,
    ) {
    }

    public function __invoke(mixed $source, array $args, mixed $context, ResolveInfo $info): mixed
    {

        $fieldName = $info->fieldName;

        if (
            $fieldName === 'absoluteFileUri' &&
            $source instanceof CurriculumInventoryReportDTO
        ) {
            return $this->decorator->getAbsoluteFileUriForDTO($source);
        }

        return $this->fieldResolver->__invoke($source, $args, $context, $info);
    }
}
