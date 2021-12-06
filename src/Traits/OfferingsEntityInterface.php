<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\OfferingInterface;

/**
 * Interface OfferingsEntityInterface
 */
interface OfferingsEntityInterface
{
    public function setOfferings(Collection $offerings);

    public function addOffering(OfferingInterface $offering);

    public function removeOffering(OfferingInterface $offering);

    public function getOfferings(): Collection;
}
