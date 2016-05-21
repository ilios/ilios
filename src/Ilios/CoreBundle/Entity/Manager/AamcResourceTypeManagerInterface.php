<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\AamcResourceTypeInterface;

/**
 * Interface AamcResourceTypeManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface AamcResourceTypeManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AamcResourceTypeInterface
     */
    public function findAamcResourceTypeBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AamcResourceTypeInterface[]
     */
    public function findAamcResourceTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param AamcResourceTypeInterface $aamcPcrs
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateAamcResourceType(
        AamcResourceTypeInterface $aamcPcrs,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param AamcResourceTypeInterface $aamcPcrs
     *
     * @return void
     */
    public function deleteAamcResourceType(
        AamcResourceTypeInterface $aamcPcrs
    );

    /**
     * @return AamcResourceTypeInterface
     */
    public function createAamcResourceType();
}
