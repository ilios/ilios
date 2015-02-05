<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\AlertInterface;

/**
 * Interface AlertManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface AlertManagerInterface
{
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
     * @param integer $limit
     * @param integer $offset
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

    /**
     * @return AlertInterface
     */
    public function createAlert();
}
