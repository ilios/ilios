<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class FilterableBy
{
    public function __construct(public string $property, public string $type)
    {
    }
}
