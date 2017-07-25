<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;

/**
 * Class StewardedEntity
 */
trait StewardedEntity
{
    /**
     * @param Collection $stewards
     */
    public function setStewards(Collection $stewards)
    {
        $this->stewards = new ArrayCollection();

        foreach ($stewards as $steward) {
            $this->addSteward($steward);
        }
    }

    /**
     * @param ProgramYearStewardInterface $steward
     */
    public function addSteward(ProgramYearStewardInterface $steward)
    {
        $this->stewards->add($steward);
    }

    /**
     * @param ProgramYearStewardInterface $steward
     */
    public function removeSteward(ProgramYearStewardInterface $steward)
    {
        $this->stewards->removeElement($steward);
    }

    /**
    * @return ProgramYearStewardInterface[]|ArrayCollection
    */
    public function getStewards()
    {
        return $this->stewards;
    }
}
