<?php

namespace Ilios\ApiBundle\Normalizer;

use Ilios\CoreBundle\Service\EntityMetadata;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Ilios DTO normalizer
 */
class DTO extends ObjectNormalizer
{
    /**
     * @var EntityMetadata
     */
    private $entityMetadata;

    /**
     * @param EntityMetadata $entityMetadata
     */
    public function setEntityMetadata(EntityMetadata $entityMetadata)
    {
        $this->entityMetadata = $entityMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $arr = parent::normalize($object, $format, $context);

        //remove null values
        return array_filter($arr, function($value) {
            return null !== $value;
        });
    }


    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($classNameOrObject, $format = null)
    {
        $this->entityMetadata->isAnIliosDto($classNameOrObject);
    }
}