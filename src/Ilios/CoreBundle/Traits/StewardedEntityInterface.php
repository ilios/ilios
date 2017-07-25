<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;

/**
 * Interface DescribableEntityInterface
 */
interface StewardedEntityInterface
{
    /**
     * @param Collection $stewards
     */
    public function setStewards(Collection $stewards);

    /**
     * @param ProgramYearStewardInterface $steward
     */
    public function addSteward(ProgramYearStewardInterface $steward);

    /**
     * @param ProgramYearStewardInterface $steward
     */
    public function removeSteward(ProgramYearStewardInterface $steward);

    /**
    * @return ProgramYearStewardInterface[]|ArrayCollection
    */
    public function getStewards();
}
