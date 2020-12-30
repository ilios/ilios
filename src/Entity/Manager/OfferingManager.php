<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use App\Repository\OfferingRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use App\Entity\OfferingInterface;

/**
 * Class OfferingManager
 */
class OfferingManager extends BaseManager
{
    /**
     * Retrieves offerings starting X days from now.
     */
    public function getOfferingsForTeachingReminders(int $daysInAdvance, array $schoolIds): array
    {
        /** @var OfferingRepository $repository */
        $repository = $this->getRepository();
        return $repository->getOfferingsForTeachingReminders($daysInAdvance, $schoolIds);
    }
}
