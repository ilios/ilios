<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\AlertInterface;

/**
 * Class AlertManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AlertManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findAlertBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findAlertsBy(
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
    public function updateAlert(
        AlertInterface $alert,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($alert, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteAlert(
        AlertInterface $alert
    ) {
        $this->delete($alert);
    }

    /**
     * @deprecated
     */
    public function createAlert()
    {
        return $this->create();
    }
}
