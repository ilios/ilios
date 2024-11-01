<?php

declare(strict_types=1);

namespace App\Service;

use App\Encoder\JsonApiEncoder;
use App\Normalizer\DTONormalizer;
use App\Normalizer\FactoryNormalizer;
use App\Normalizer\JsonApiDTONormalizer;
use App\Denormalizer\EntityDenormalizer;
use App\Normalizer\EntityNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class SerializerFactory
 * Ensures that we only serialize the things we want to
 * and don't fall back to any defaults.
 *
 */
class SerializerFactory
{
    /**
     * Build our own serializer just the way we like it
     */
    public static function createSerializer(
        FactoryNormalizer $factoryNormalizer,
        DTONormalizer $dtoNormalizer,
        JsonApiDTONormalizer $jsonApiDTONormalizer,
        JsonApiEncoder $jsonApiEncoder,
        EntityDenormalizer $entityDenormalizer,
        EntityNormalizer $entityNormalizer
    ): Serializer {
        $jsonEncoder = new JsonEncoder();
        $array = new ArrayDenormalizer();
        $dateTime = new DateTimeNormalizer();
        $serializer =  new Serializer(
            [
                $array,
                $dateTime,
                $factoryNormalizer,
                $jsonApiDTONormalizer,
                $entityNormalizer,
                $entityDenormalizer,
                $dtoNormalizer,
            ],
            [
                $jsonApiEncoder,
                $jsonEncoder,
            ]
        );

        $factoryNormalizer->setNormalizer($serializer);

        return $serializer;
    }
}
