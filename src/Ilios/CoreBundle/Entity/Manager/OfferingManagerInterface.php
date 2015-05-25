<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * Interface OfferingManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface OfferingManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return OfferingInterface
     */
    public function findOfferingBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|OfferingInterface[]
     */
    public function findOfferingsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param OfferingInterface $offering
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateOffering(
        OfferingInterface $offering,
        $andFlush = true,
        $forceId  = false
    );

    /**
     * @param OfferingInterface $offering
     *
     * @return void
     */
    public function deleteOffering(
        OfferingInterface $offering
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return OfferingInterface
     */
    public function createOffering();
}
