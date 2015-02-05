<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * SessionType manager service.
 * Class SessionTypeManager
 * @package Ilios\CoreBundle\Manager
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
     * @return SessionTypeInterface
     */
    public function findSessionTypeBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SessionTypeInterface[]|Collection
     */
    public function findSessionTypesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param SessionTypeInterface $sessionType
     * @param bool $andFlush
     */
    public function updateSessionType(SessionTypeInterface $sessionType, $andFlush = true)
    {
        $this->em->persist($sessionType);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param SessionTypeInterface $sessionType
     */
    public function deleteSessionType(SessionTypeInterface $sessionType)
    {
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
