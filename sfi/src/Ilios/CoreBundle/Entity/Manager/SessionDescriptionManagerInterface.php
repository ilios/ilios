<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\SessionDescriptionInterface;

/**
 * Interface SessionDescriptionManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface SessionDescriptionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionDescriptionInterface
     */
    public function findSessionDescriptionBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SessionDescriptionInterface[]|Collection
     */
    public function findSessionDescriptionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param SessionDescriptionInterface $sessionDescription
     * @param bool $andFlush
     *
     * @return void
     */
     public function updateSessionDescription(SessionDescriptionInterface $sessionDescription, $andFlush = true);

    /**
     * @param SessionDescriptionInterface $sessionDescription
     *
     * @return void
     */
    public function deleteSessionDescription(SessionDescriptionInterface $sessionDescription);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return SessionDescriptionInterface
     */
    public function createSessionDescription();
}