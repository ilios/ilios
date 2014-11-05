<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\AlertInterface;

/**
 * Interface AlertManagerInterface
 */
interface AlertManagerInterface
{
    /** 
     *@return AlertInterface
     */
    public function createAlert();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AlertInterface
     */
    public function findAlertBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return AlertInterface[]|Collection
     */
    public function findAlertsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param AlertInterface $alert
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAlert(AlertInterface $alert, $andFlush = true);

    /**
     * @param AlertInterface $alert
     *
     * @return void
     */
    public function deleteAlert(AlertInterface $alert);

    /**
     * @return string
     */
    public function getClass();
}
