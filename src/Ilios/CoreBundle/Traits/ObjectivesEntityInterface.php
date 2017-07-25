<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * Interface ObjectivesEntityInterface
 */
interface ObjectivesEntityInterface
{
    /**
     * @param Collection $objectives
     */
    public function setObjectives(Collection $objectives);

    /**
     * @param ObjectiveInterface $objective
     */
    public function addObjective(ObjectiveInterface $objective);

    /**
     * @param ObjectiveInterface $objective
     */
    public function removeObjective(ObjectiveInterface $objective);

    /**
    * @return ObjectiveInterface[]|ArrayCollection
    */
    public function getObjectives();
}
