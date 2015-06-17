<?php

namespace Ilios\CoreBundle\Form\DataTransformer;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
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
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * The name of the entity we are working with
     * @var string
     */
     protected $entityName;

    /**
     * @param Registry $registry
     * @param string $entityName
     */
    public function __construct(Registry $registry, $entityName)
    {
        $this->em         = $registry->getManagerForClass($entityName);
        $this->repository = $registry->getRepository($entityName);
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

        $entity = $this->repository->find($id);

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
