<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\AamcPcrsInterface;

/**
 * Interface AamcPcrsManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface AamcPcrsManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AamcPcrsInterface
     */
    public function findAamcPcrsBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|AamcPcrsInterface[]
     */
    public function findAamcPcrsesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param AamcPcrsInterface $aamcPcrs
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateAamcPcrs(
        AamcPcrsInterface $aamcPcrs,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param AamcPcrsInterface $aamcPcrs
     *
     * @return void
     */
    public function deleteAamcPcrs(
        AamcPcrsInterface $aamcPcrs
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return AamcPcrsInterface
     */
    public function createAamcPcrs();
}
