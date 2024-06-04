<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;
use InvalidArgumentException;

/**
 * Indicates the type of data contained in the property
 * some amount of type casting takes place to convert to
 * and from the designated type.
 *
 * Entity: The value will be converted to the related objects
 * id in JSON and converted back into the entity it represents
 *
 * entityCollection: presented as an array of ids and converted
 * into and array of entities when it is PUT
 *
 * array<string> generally the DTO representation of an entity collection
 * we just have the IDs in an array and each of them is a string
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Type
{
    public const string VALUE = 'value';
    public const string STRINGS = 'array<string>';
    public const string INTEGERS = 'array<integer>';
    public const string ENTITY_COLLECTION = 'entityCollection';
    public const string DTOS = 'array<dto>';
    private const array ALLOWED_VALUES = [
        'integer',
        'float',
        'string',
        'boolean',
        'dateTime',
        'entity',
        self::ENTITY_COLLECTION,
        'array',
        self::STRINGS,
        self::INTEGERS,
        self::DTOS,
    ];

    public string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::ALLOWED_VALUES)) {
            throw new InvalidArgumentException("{$value} is not a valid type");
        }
        $this->value = $value;
    }
}
