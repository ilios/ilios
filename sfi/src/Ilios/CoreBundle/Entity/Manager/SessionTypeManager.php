<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\SessionTypeManager as BaseSessionTypeManager;
use Ilios\CoreBundle\Model\SessionTypeInterface;

class SessionTypeManager extends BaseSessionTypeManager
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
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
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
     *
     * @return void
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
     *
     * @return void
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
}
