<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\AamcPcrsInterface;

/**
 * Interface AamcPcrsManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface AamcPcrsManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AamcPcrsInterface
     */
    public function findAamcPcrsBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AamcPcrsInterface[]|Collection
     */
    public function findAamcPcrsesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param AamcPcrsInterface $aamcPcrs
     * @param bool $andFlush
     *
     * @return void
     */
     public function updateAamcPcrs(AamcPcrsInterface $aamcPcrs, $andFlush = true);

    /**
     * @param AamcPcrsInterface $aamcPcrs
     *
     * @return void
     */
    public function deleteAamcPcrs(AamcPcrsInterface $aamcPcrs);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return AamcPcrsInterface
     */
    public function createAamcPcrs();
}
