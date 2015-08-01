<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\AamcMethodInterface;

/**
 * Interface AamcMethodManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface AamcMethodManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AamcMethodInterface
     */
    public function findAamcMethodBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|AamcMethodInterface[]
     */
    public function findAamcMethodsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param AamcMethodInterface $aamcMethod
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateAamcMethod(
        AamcMethodInterface $aamcMethod,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param AamcMethodInterface $aamcMethod
     *
     * @return void
     */
    public function deleteAamcMethod(
        AamcMethodInterface $aamcMethod
    );

    /**
     * @return AamcMethodInterface
     */
    public function createAamcMethod();
}
