<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * Interface OfferingManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface OfferingManagerInterface
{
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
     * @param integer $limit
     * @param integer $offset
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

    /**
     * @return OfferingInterface
     */
    public function createOffering();
}
