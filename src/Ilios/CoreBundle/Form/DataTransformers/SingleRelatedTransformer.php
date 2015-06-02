<?php

namespace Ilios\CoreBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class RelatedTransformer
 *
 * Transforms Relationship data for entity based forms
 *
 */
class SingleRelatedTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * The name of the entity we are working with
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
     * Transforms an entity to a string (id).
     *
     * @param  string|null $entity
     * @return string
     */
    public function transform($entity)
    {
        if (empty($entity)) {
            return null;
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
