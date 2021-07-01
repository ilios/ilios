<?php

declare(strict_types=1);

namespace App\Attribute;

use Attribute;

/**
 * Properties which can be read in the API, but
 * cannot be written to.  Any attempt to write to
 * them will be silently ignored in the API.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ReadOnly
{
}
