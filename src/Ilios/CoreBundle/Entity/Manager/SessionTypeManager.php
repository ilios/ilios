<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * Class SessionTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionTypeManager implements SessionTypeManagerInterface
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
     * @return SessionTypeInterface
     */
    public function findSessionTypeBy(
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
     * @return ArrayCollection|SessionTypeInterface[]
     */
    public function findSessionTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param SessionTypeInterface $sessionType
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateSessionType(
        SessionTypeInterface $sessionType,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($sessionType);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($sessionType));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param SessionTypeInterface $sessionType
     */
    public function deleteSessionType(
        SessionTypeInterface $sessionType
    ) {
        $this->em->remove($sessionType);
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
     * @return SessionTypeInterface
     */
    public function createSessionType()
    {
        $class = $this->getClass();
        return new $class();
    }
}
