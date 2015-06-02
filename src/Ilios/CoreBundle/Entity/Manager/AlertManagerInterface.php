<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\AlertInterface;

/**
 * Interface AlertManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface AlertManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AlertInterface
     */
    public function findAlertBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|AlertInterface[]
     */
    public function findAlertsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param AlertInterface $alert
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateAlert(
        AlertInterface $alert,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param AlertInterface $alert
     *
     * @return void
     */
    public function deleteAlert(
        AlertInterface $alert
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return AlertInterface
     */
    public function createAlert();
}
