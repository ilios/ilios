<?php

declare(strict_types=1);

namespace App\Annotation;

/**
 * Information About related resources
 * @Annotation
 * @Target("PROPERTY")
 */
class Related
{
    /** @Required */
    public $value;
}
