<?php

namespace Ilios\CoreBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;

class ArrayToIdTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string Name spaced entity
     */
    private $class;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @param EntityManager $em
     * @param string        $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function transform($data)
    {
        return $data;
    }

    /**
     * Transforms a string or array into an id.
     */
    public function reverseTransform($data)
    {

        if (!$data) {
            return null;
        }

        if (is_scalar($data)) {
            return $data;
        }

        if (is_array($data) && null !== $this->getEntityIdentifier()) {
            return $this->getEntityIdentifier();
        }

        return null;
    }


    /**
     * @throws \RuntimeException
     * @return string
     */
    protected function getEntityIdentifier()
    {
        if (count($this->getMetaData()) > 1) {
            throw new \RuntimeException(
                'SfProjectGeneratorBundle is incompatible with entities that contain more than one identifier.'
            );
        }

        return $this->getMetaData()[0];
    }

    /**
     * @return array
     */
    protected function getMetaData()
    {
        if (null === $this->metadata) {
            $this->metadata = $this->em->getClassMetadata($this->class)->getMetaData();
        }

        return $this->metadata;
    }
}
