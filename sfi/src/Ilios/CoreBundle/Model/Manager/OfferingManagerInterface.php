<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\OfferingInterface;

/**
 * Interface OfferingManagerInterface
 */
interface OfferingManagerInterface
{
    /** 
     *@return OfferingInterface
     */
    public function createOffering();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return OfferingInterface
     */
    public function findOfferingBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return OfferingInterface[]|Collection
     */
    public function findOfferingsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param OfferingInterface $offering
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateOffering(OfferingInterface $offering, $andFlush = true);

    /**
     * @param OfferingInterface $offering
     *
     * @return void
     */
    public function deleteOffering(OfferingInterface $offering);

    /**
     * @return string
     */
    public function getClass();
}
