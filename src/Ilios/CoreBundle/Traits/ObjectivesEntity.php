<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * Class ObjectivesEntity
 */
trait ObjectivesEntity
{
    /**
     * @inheritdoc
     */
    public function setObjectives(Collection $objectives)
    {
        $this->objectives = new ArrayCollection();

        foreach ($objectives as $objective) {
            $this->addObjective($objective);
        }
    }

    /**
     * @inheritdoc
     */
    public function addObjective(ObjectiveInterface $objective)
    {
        if (!$this->objectives->contains($objective)) {
            $this->objectives->add($objective);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeObjective(ObjectiveInterface $objective)
    {
        $this->objectives->removeElement($objective);
    }

    /**
    * @inheritdoc
    */
    public function getObjectives()
    {
        return $this->objectives;
    }
}
