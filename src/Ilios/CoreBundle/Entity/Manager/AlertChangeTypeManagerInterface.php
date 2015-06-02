<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

/**
 * Interface AlertChangeTypeManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface AlertChangeTypeManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AlertChangeTypeInterface
     */
    public function findAlertChangeTypeBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|AlertChangeTypeInterface[]
     */
    public function findAlertChangeTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateAlertChangeType(
        AlertChangeTypeInterface $alertChangeType,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     *
     * @return void
     */
    public function deleteAlertChangeType(
        AlertChangeTypeInterface $alertChangeType
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return AlertChangeTypeInterface
     */
    public function createAlertChangeType();
}
