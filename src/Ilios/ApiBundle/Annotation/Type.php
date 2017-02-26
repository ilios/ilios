<?php

namespace Ilios\ApiBundle\Annotation;

/**
 * Indicates the type of data contained in the property
 * some ammount of type casting takes place to convert to
 * and from the designated type.
 *
 * Entity: The value will be converted to the related objects
 * id in JSON and converted back into the entity it represents
 *
 * entityCollection: presented as an array of ids and convereted
 * into and array of entities when it is iput
 *
 * array<string> generally the DTO representation of an entity collection
 * we just have the IDs in an array and each of them is a string
 *
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
