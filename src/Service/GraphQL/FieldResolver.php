<?php

declare(strict_types=1);

namespace App\Service\GraphQL;

use App\Entity\DTO\SchoolDTO;
use App\Repository\SchoolRepository;
use ArrayAccess;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;

class FieldResolver
{
    public function __invoke(mixed $source, array $args, mixed $context, ResolveInfo $info): mixed
    {
        $fieldName = $info->fieldName;
        $property = null;

        if (is_array($source) || $source instanceof ArrayAccess) {
            if (isset($source[$fieldName])) {
                $property = $source[$fieldName];
            }
        } elseif (is_object($source)) {
            if (isset($source->{$fieldName})) {
                $property = $source->{$fieldName};
            }
        }

        return $property instanceof Closure ? $property($source, $args, $context, $info) : $property;
    }
}
