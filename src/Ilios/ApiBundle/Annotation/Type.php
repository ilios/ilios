<?php

namespace Ilios\ApiBundle\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Type
{
    /**
     * @Enum({"integer", "string", "boolean", "dateTime", "entity", "entityCollection"})
     */
    public $value;
}