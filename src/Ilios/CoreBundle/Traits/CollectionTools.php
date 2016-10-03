<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Collection;

/**
 * Class CollectionTools
 * @package Ilios\CoreBundle\Traits
 */
trait CollectionTools
{

    /**
     * Parse two collections and get the elements that have changed
     *
     * @param Collection $original
     * @param Collection $new
     * @return array
     */
    protected function diffCollection(Collection $original, Collection $new)
    {
        $rhett = [];
        $rhett['deleted'] = $original->filter(function ($item) use ($new) {
            return !$new->contains($item);
        });
        $rhett['new'] = $new->filter(function ($item) use ($original) {
            return !$original->contains($item);
        });

        return $rhett;
    }

    /**
     * Handle the minutia of dealing with related entities
     * @param string $property the name of the property we are working with
     * @param string $addMethod the name of the method we will call to add new elements to the property
     * @param string $removeMethod the name of the method we will call to remove old elements from the property
     * @param Collection | null $newValues the collection that represents the new state for the property
     */
    protected function setRelationship($property, $addMethod, $removeMethod, Collection $newValues = null)
    {
        if (is_null($newValues)) {
            $this->$property = new ArrayCollection();
            return;
        }
        $diff = $this->diffCollection($this->$property, $newValues);

        foreach ($diff['new'] as $item) {
            $this->$addMethod($item);
        }
        foreach ($diff['deleted'] as $item) {
            $this->$removeMethod($item);
        }
    }
}
