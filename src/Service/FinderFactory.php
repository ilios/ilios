<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Finder\Finder;

/**
 * Convenience Service for getting a Finder injected which is
 * useful when we need to test something where the finder is used.
 * Class FinderFactory
 * @package App\Service
 */
class FinderFactory
{

    /**
     * @return Finder
     */
    public static function create(): Finder
    {
        return new Finder();
    }
}
