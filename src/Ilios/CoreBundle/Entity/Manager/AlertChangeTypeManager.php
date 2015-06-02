<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

/**
 * Class AlertChangeTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @return ArrayCollection|AlertChangeTypeInterface[]
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
     * @param bool $forceId
     */
    public function updateAlertChangeType(
        AlertChangeTypeInterface $alertChangeType,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($alertChangeType);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($alertChangeType));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

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
