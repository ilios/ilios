<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\AamcPcrsInterface;

/**
 * Interface AamcPcrsManagerInterface
 */
interface AamcPcrsManagerInterface
{
    /** 
     *@return AamcPcrsInterface
     */
    public function createAamcPcrs();

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
    public function findAamcPcrsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

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
}
