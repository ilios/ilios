<?php

namespace Ilios\CoreBundle\Form\Transformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

use Ilios\CoreBundle\Traits\StringableEntityInterface;

/**
 * Class RelatedTransformer
 *
 * Transforms Relationship data for entity based forms
 *
 * @package Ilios\CoreBundle\DataTransformer
 *
 */
class SingleRelatedTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * The name of the entity we are working with eg IliosCoreBundle:Course
     * @var string
     */
    private $entityName;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, $entityName)
    {
        $this->om = $om;
        $this->entityName = $entityName;
    }

    /**
     * Transforms an entity to a string (number).
     *
     * @param  string|null $entity
     * @return string
     */
    public function transform($entity)
    {
        if (empty($entity)) {
            return null;
        }

        if (!$entity instanceof StringableEntityInterface ) {
            throw new TransformationFailedException(sprintf(
                '%s is not an instance of %s',
                is_object($entity)?get_class($entity):$entity,
                'StringableEntityInterface'
            ));
        }

        return (string) $entity;
    }

    /**
     * Transforms an id to an entity.
     *
     * @param  string $id
     *
     * @return mixed
     *
     * @throws TransformationFailedException if entiyt is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $entity = $this->om
            ->getRepository($this->entityName)
            ->find($id)
        ;

        if (null === $entity) {
            throw new TransformationFailedException(sprintf(
                'A %s with id "%s" does not exist!',
                $this->entityName,
                $id
            ));
        }

        return $entity;
    }

}
