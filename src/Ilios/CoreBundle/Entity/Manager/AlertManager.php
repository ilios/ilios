<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AlertInterface;

/**
 * Class AlertManager
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @param Registry $em
     * @param string $class
     */
    public function __construct(Registry $em, $class)
    {
        $this->em         = $em->getManagerForClass($class);
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
     * @return ArrayCollection|AlertInterface[]
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
     * @param bool $forceId
     */
    public function updateAlert(
        AlertInterface $alert,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($alert);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($alert));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

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
