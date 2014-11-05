<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\AlertChangeTypeInterface;

/**
 * Interface AlertChangeTypeManagerInterface
 */
interface AlertChangeTypeManagerInterface
{
    /** 
     *@return AlertChangeTypeInterface
     */
    public function createAlertChangeType();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AlertChangeTypeInterface
     */
    public function findAlertChangeTypeBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return AlertChangeTypeInterface[]|Collection
     */
    public function findAlertChangeTypesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAlertChangeType(AlertChangeTypeInterface $alertChangeType, $andFlush = true);

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     *
     * @return void
     */
    public function deleteAlertChangeType(AlertChangeTypeInterface $alertChangeType);

    /**
     * @return string
     */
    public function getClass();
}
