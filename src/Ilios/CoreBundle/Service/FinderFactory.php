<?php

namespace Ilios\CoreBundle\Service;

use Symfony\Component\Finder\Finder;

/**
 * Convenience Service for getting a Finder injected which is
 * useful when we need to test something where the finder is used.
 * Class FinderFactory
 * @package Ilios\CoreBundle\Service
 */
class FinderFactory
{

    /**
     * @return Finder
     */
    public static function create()
    {
        return new Finder();
    }
}
