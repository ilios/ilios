<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

/**
 * AlertChangeType manager service.
 * Class AlertChangeTypeManager
 * @package Ilios\CoreBundle\Manager
 */
class AlertChangeTypeManager implements AlertChangeTypeManagerInterface
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
     * @return AlertChangeTypeInterface
     */
    public function findAlertChangeTypeBy(
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
     * @return AlertChangeTypeInterface[]|Collection
     */
    public function findAlertChangeTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     * @param bool $andFlush
     */
    public function updateAlertChangeType(
        AlertChangeTypeInterface $alertChangeType,
        $andFlush = true
    ) {
        $this->em->persist($alertChangeType);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     */
    public function deleteAlertChangeType(
        AlertChangeTypeInterface $alertChangeType
    ) {
        $this->em->remove($alertChangeType);
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
     * @return AlertChangeTypeInterface
     */
    public function createAlertChangeType()
    {
        $class = $this->getClass();
        return new $class();
    }
}
