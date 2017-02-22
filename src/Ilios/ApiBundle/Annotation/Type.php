<?php

namespace Ilios\ApiBundle\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Type
{
    /**
     * @Enum({"integer", "float", "string", "boolean", "dateTime", "entity", "entityCollection", "array<string>"})
     */
    public $value;
}
