<?php

declare(strict_types=1);

namespace App\Attribute;

use Attribute;

/**
 * Apply this to a property in order to purify
 * the submitted content and remove any non-standard
 * or harmful markup.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class RemoveMarkup
{
}
