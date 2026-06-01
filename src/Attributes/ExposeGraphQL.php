<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ExposeGraphQL
{
    public function __construct(public ?string $customResolver = null)
    {
    }
}
