<?php

namespace Ilios\Corebundle\Form\DataTransformer;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class RelatedTransformer
 *
 * Transforms Relationship data for entity based forms
 *
 */
class ManyRelatedTransformer implements DataTransformerInterface
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
     * Transforms an entity collection to an array of strings.
     *
     * @param  array|null $array
     * @return string[]
     */
    public function transform($collection)
    {
        if (empty($collection)) {
            return [];
        }

        if (!$collection instanceof Collection) {
            throw new TransformationFailedException(sprintf(
                '%s is not an instance of %s',
                gettype($collection),
                'Doctrine\Common\Collections\Collection'
            ));
        }

        return $collection->map(function ($entity) {
            return (string) $entity;
        })->toArray();
    }

    /**
     * Transforms an array of ids to an array of entities.
     *
     * @param  Collection|array $collection
     *
     * @return mixed
     *
     * @throws TransformationFailedException if entity is not found.
     */
    public function reverseTransform($collection)
    {
        //convert plain arrays to a doctrine collection
        if (is_array($collection)) {
            $collection = new ArrayCollection($collection);
        }

        if (!$collection instanceof Collection) {
            throw new TransformationFailedException(sprintf(
                '%s is not an instance of %s',
                gettype($collection),
                'Doctrine\Common\Collections\Collection'
            ));
        }

        if ($collection->isEmpty()) {
            return $collection;
        }

        return $collection->map(function ($id) {
            $entity = $this->repository->find($id);

            if (null === $entity) {
                throw new TransformationFailedException(sprintf(
                    'A %s with id "%s" does not exist!',
                    $this->entityName,
                    $id
                ));
            }

            return $entity;
        });
    }
}
