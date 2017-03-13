<?php

namespace Ilios\ApiBundle\Annotation;

/**
 * Properties which can be read in the API, but
 * cannot be written to.  Any attempt to write to
 * them will be silently ignored in the API.
 * @Annotation
 * @Target("PROPERTY")
 */
class ReadOnly
{

}
