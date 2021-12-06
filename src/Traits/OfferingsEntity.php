<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\OfferingInterface;

/**
 * Class OfferingsEntity
 */
trait OfferingsEntity
{
    public function setOfferings(Collection $offerings)
    {
        $this->offerings = new ArrayCollection();

        foreach ($offerings as $offering) {
            $this->addOffering($offering);
        }
    }

    public function addOffering(OfferingInterface $offering)
    {
        if (!$this->offerings->contains($offering)) {
            $this->offerings->add($offering);
        }
    }

    public function removeOffering(OfferingInterface $offering)
    {
        $this->offerings->removeElement($offering);
    }

    public function getOfferings(): Collection
    {
        return $this->offerings;
    }
}
