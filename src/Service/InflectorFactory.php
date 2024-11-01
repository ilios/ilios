<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\Inflector\Inflector as DoctrineInflector;
use Doctrine\Inflector\InflectorFactory as DoctrineInflectorFactory;
use Doctrine\Inflector\Rules\Pattern;
use Doctrine\Inflector\Rules\Patterns;
use Doctrine\Inflector\Rules\Ruleset;
use Doctrine\Inflector\Rules\Substitutions;
use Doctrine\Inflector\Rules\Transformation;
use Doctrine\Inflector\Rules\Transformations;

/**
 * Our own static proxy to the doctrine inflector
 * so we only have to defines rules in a single place.
 */
class InflectorFactory
{
    protected static ?DoctrineInflector $inflector = null;

    public static function create(): DoctrineInflector
    {
        if (!self::$inflector) {
            self::$inflector =  DoctrineInflectorFactory::create()
                ->withSingularRules(
                    new Ruleset(
                        new Transformations(
                            new Transformation(new Pattern('/^aamc(p)crses$/i'), 'aamc\1crs'),
                        ),
                        new Patterns(new Pattern("aamcpcrs")),
                        new Substitutions()
                    )
                )
                ->withPluralRules(
                    new Ruleset(
                        new Transformations(
                            new Transformation(new Pattern('/^aamc(p)crs$/i'), 'aamc\1crses'),
                        ),
                        new Patterns(new Pattern("aamcpcrses")),
                        new Substitutions()
                    )
                )
                ->build();
        }

        return self::$inflector;
    }
}
