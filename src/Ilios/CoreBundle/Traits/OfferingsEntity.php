<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * Class OfferingsEntity
 */
trait OfferingsEntity
{
    /**
     * @param Collection $offerings
     */
    public function setOfferings(Collection $offerings)
    {
        $this->offerings = new ArrayCollection();

        foreach ($offerings as $offering) {
            $this->addOffering($offering);
        }
    }

    /**
     * @param OfferingInterface $offering
     */
    public function addOffering(OfferingInterface $offering)
    {
        if (!$this->offerings->contains($offering)) {
            $this->offerings->add($offering);
        }
    }

    /**
     * @param OfferingInterface $offering
     */
    public function removeOffering(OfferingInterface $offering)
    {
        $this->offerings->removeElement($offering);
    }

    /**
    * @return OfferingInterface[]|ArrayCollection
    */
    public function getOfferings()
    {
        return $this->offerings;
    }
}
