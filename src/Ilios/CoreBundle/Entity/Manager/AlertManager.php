<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\AlertInterface;

/**
 * Alert manager service.
 * Class AlertManager
 * @package Ilios\CoreBundle\Manager
 */
class AlertManager implements AlertManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AlertInterface
     */
    public function findAlertBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AlertInterface[]|Collection
     */
    public function findAlertsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AlertInterface $alert
     * @param bool $andFlush
     */
    public function updateAlert(
        AlertInterface $alert,
        $andFlush = true
    ) {
        $this->em->persist($alert);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AlertInterface $alert
     */
    public function deleteAlert(
        AlertInterface $alert
    ) {
        $this->em->remove($alert);
        $this->em->flush();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return AlertInterface
     */
    public function createAlert()
    {
        $class = $this->getClass();
        return new $class();
    }
}
