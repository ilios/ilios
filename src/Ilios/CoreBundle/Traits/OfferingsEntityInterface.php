<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * Interface OfferingsEntityInterface
 */
interface OfferingsEntityInterface
{
    /**
     * @param Collection $offerings
     */
    public function setOfferings(Collection $offerings);

    /**
     * @param OfferingInterface $offering
     */
    public function addOffering(OfferingInterface $offering);

    /**
     * @param OfferingInterface $offering
     */
    public function removeOffering(OfferingInterface $offering);

    /**
    * @return OfferingInterface[]|ArrayCollection
    */
    public function getOfferings();
}
