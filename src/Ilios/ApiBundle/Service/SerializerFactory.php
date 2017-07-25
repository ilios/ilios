<?php

namespace Ilios\ApiBundle\Service;

use Ilios\ApiBundle\Normalizer\DTO;
use Ilios\ApiBundle\Normalizer\Entity;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

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
     * @param Entity $entityNormalizer
     * @param DTO $dtoNormalizer
     * @return Serializer
     */
    public static function createSerializer(Entity $entityNormalizer, DTO $dtoNormalizer)
    {
        $jsonEncoder = new JsonEncoder();
        $array = new ArrayDenormalizer();
        $dateTime = new DateTimeNormalizer();
        $processors = [$array, $dateTime, $jsonEncoder, $entityNormalizer, $dtoNormalizer];
        return new Serializer($processors, $processors);
    }
}
