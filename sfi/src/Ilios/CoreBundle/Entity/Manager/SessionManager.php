<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\SessionInterface;

/**
 * Session manager service.
 * Class SessionManager
 * @package Ilios\CoreBundle\Manager
 */
class SessionManager implements SessionManagerInterface
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
     * @return SessionInterface
     */
    public function findSessionBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SessionInterface[]|Collection
     */
    public function findSessionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param SessionInterface $session
     * @param bool $andFlush
     */
    public function updateSession(SessionInterface $session, $andFlush = true)
    {
        $this->em->persist($session);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param SessionInterface $session
     */
    public function deleteSession(SessionInterface $session)
    {
        $this->em->remove($session);
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
     * @return SessionInterface
     */
    public function createSession()
    {
        $class = $this->getClass();
        return new $class();
    }
}
