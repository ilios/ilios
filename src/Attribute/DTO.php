<?php

declare(strict_types=1);

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class DTO
{
    public const VALUE = 'value';

    public function __construct(public string $value)
    {
    }
}
