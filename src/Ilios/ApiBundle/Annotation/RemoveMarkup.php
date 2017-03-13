<?php

namespace Ilios\ApiBundle\Annotation;

/**
 * Apply this to a property in order to purify
 * the submitted content and remove any non-standard
 * or harmful markup.
 * @Annotation
 * @Target("PROPERTY")
 */
class RemoveMarkup
{

}
