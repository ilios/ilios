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
     * Handle the minutia of dealting with related entities
     * @param $property
     * @param $newValues
     * @param $addMethod
     * @param $removeMethod
     */
    protected function setRelationship($property, $newValues, $addMethod, $removeMethod)
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
