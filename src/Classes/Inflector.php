<?php

declare(strict_types=1);

namespace App\Classes;

use Doctrine\Inflector\InflectorFactory;
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
class Inflector
{
    protected static $inflector;

    protected static function getInflector(): \Doctrine\Inflector\Inflector
    {
        if (!self::$inflector) {
            self::$inflector = InflectorFactory::create()
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

    public static function singularize(string $word): string
    {
        return self::getInflector()->singularize($word);
    }

    public static function pluralize(string $word): string
    {
        return self::getInflector()->pluralize($word);
    }

    public static function camelize(string $word): string
    {
        return self::getInflector()->camelize($word);
    }
}
