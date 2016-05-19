<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\AamcMethodInterface;

/**
 * Class AamcMethodManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AamcMethodManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findAamcMethodBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findAamcMethodsBy(
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
    public function updateAamcMethod(
        AamcMethodInterface $aamcMethod,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($aamcMethod, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteAamcMethod(
        AamcMethodInterface $aamcMethod
    ) {
        $this->delete($aamcMethod);
    }

    /**
     * @deprecated
     */
    public function createAamcMethod()
    {
        return $this->create();
    }
}
