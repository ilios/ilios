<?php

namespace Ilios\CoreBundle\Form\Transformer;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
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
class ManyRelatedTransformer implements DataTransformerInterface
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
     * Transforms an entity to an array of strings.
     *
     * @param  array|null $array
     * @return string[]
     */
    public function transform($collection)
    {
        if (empty($collection)) {
            return [];
        }

        if (!$collection instanceof Collection ) {
            throw new TransformationFailedException(sprintf(
                '%s is not an instance of %s',
                is_object($collection)?get_class($collection):$collection,
                'Collection'
            ));
        }

        return $collection->map(function ($entity) {
            return (string) $entity;
        })->toArray();
    }

    /**
     * Transforms an array of ids to an entity.
     *
     * @param  Collection|array $collection
     *
     * @return mixed
     *
     * @throws TransformationFailedException if entiyt is not found.
     */
    public function reverseTransform($collection)
    {
        //convert plain arrays to a doctrine collection
        if (is_array($collection)) {
            $collection = new ArrayCollection($collection);
        }

        if (!$collection instanceof Collection ) {
            throw new TransformationFailedException(sprintf(
                '%s is not an instance of %s',
                is_object($collection)?get_class($collection):$collection,
                'Collection'
            ));
        }

        if ($collection->isEmpty()) {
            return $collection;
        }

        return $collection->map(function ($id) {
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
        });
    }

}
