<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

/**
 * Class AlertChangeTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AlertChangeTypeManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findAlertChangeTypeBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findAlertChangeTypesBy(
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
    public function updateAlertChangeType(
        AlertChangeTypeInterface $alertChangeType,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($alertChangeType, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteAlertChangeType(
        AlertChangeTypeInterface $alertChangeType
    ) {
        $this->delete($alertChangeType);
    }

    /**
     * @deprecated
     */
    public function createAlertChangeType()
    {
       return $this->create();
    }
}
