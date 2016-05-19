<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\AamcPcrsInterface;

/**
 * Class AamcPcrsManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AamcPcrsManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findAamcPcrsBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findAamcPcrsesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateAamcPcrs(
        AamcPcrsInterface $aamcPcrs,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($aamcPcrs, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteAamcPcrs(
        AamcPcrsInterface $aamcPcrs
    ) {
        $this->delete($aamcPcrs);
    }

    /**
     * @deprecated
     */
    public function createAamcPcrs()
    {
        return $this->create();
    }
}
