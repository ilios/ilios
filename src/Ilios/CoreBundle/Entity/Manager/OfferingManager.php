<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * Class OfferingManager
 */
class OfferingManager extends BaseManager
{
    /**
     * Retrieves offerings starting X days from now.
     *
     * @param int $daysInAdvance Days in advance from now.
     * @return Collection|OfferingInterface[]
     */
    public function getOfferingsForTeachingReminders($daysInAdvance)
    {
        $now = time();
        $startDate = new \DateTime();
        $startDate->setTimezone(new \DateTimeZone('UTC'));
        $startDate->setTimestamp($now);
        $startDate->modify("midnight +{$daysInAdvance} days");

        $daysInAdvance++;
        $endDate = new \DateTime();
        $endDate->setTimezone(new \DateTimeZone('UTC'));
        $endDate->setTimestamp($now);
        $endDate->modify("midnight +{$daysInAdvance} days");

        $criteria = Criteria::create();
        $expr = Criteria::expr();
        $criteria->where(
            $expr->andX(
                $expr->gte('startDate', $startDate),
                $expr->lt('startDate', $endDate)
            )
        );
        $offerings = $this->getRepository()->matching($criteria);

        // filter out any offerings belonging to unpublished sessions and courses
        $offerings = $offerings->filter(function (OfferingInterface $offering) {
            return ($offering->getSession()->isPublished() && $offering->getSession()->getCourse()->isPublished());
        });

        return $offerings;
    }
}
