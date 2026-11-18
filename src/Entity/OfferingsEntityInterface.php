<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\OfferingInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface OfferingsEntityInterface
 */
interface OfferingsEntityInterface
{
    public function setOfferings(Collection $offerings): void;

    public function addOffering(OfferingInterface $offering): void;

    public function removeOffering(OfferingInterface $offering): void;

    public function getOfferings(): Collection;
}
