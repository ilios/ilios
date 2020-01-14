<?php

declare(strict_types=1);

namespace App\Annotation;

/**
 * Applied to classes which should be serialized as entities
 * Also used for de-normalizing json back into an entity
 * @Annotation
 * @Target("CLASS")
 */
class Entity
{

}
