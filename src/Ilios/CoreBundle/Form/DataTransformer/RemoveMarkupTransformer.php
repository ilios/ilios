<?php

namespace Ilios\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Data sanitation transformer.
 *
 * Class RemoveMarkupTransformer
 * @package Ilios\CoreBundle\Form\DataTransformer
 */
class RemoveMarkupTransformer implements DataTransformerInterface
{
    /**
     * Strips all tags from a given string and returns it. Any non-string input will be returned as-is.
     * @param mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        if (! is_string($value)) {
            return $value;
        }
        return strip_tags($value);
    }

    /**
     * Does nothing, it just returns the given value.
     *
     * @param string $value
     * @return string
     */
    public function reverseTransform($value)
    {
        return $value;
    }
}