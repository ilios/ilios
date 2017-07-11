<?php

namespace Ilios\ApiBundle\Annotation;

/**
 * Apply this to a property in order to purify
 * the submitted content but allow some non-harmfull HTML
 * without this property all HTML tags will be converted into
 * safe entities.
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class AllowCleanMarkup
{

}
