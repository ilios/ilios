<?php

declare(strict_types=1);

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class DTO
{
    public function __construct(public string $name, public ?string $repository = null)
    {
    }
}
