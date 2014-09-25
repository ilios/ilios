<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\CISessionManager as BaseCISessionManager;
use Ilios\CoreBundle\Model\CISessionInterface;

class CISessionManager extends BaseCISessionManager
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
     * @return CISessionInterface
     */
    public function findCISessionBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CISessionInterface[]|Collection
     */
    public function findCISessionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CISessionInterface $cISession
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCISession(CISessionInterface $cISession, $andFlush = true)
    {
        $this->em->persist($cISession);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CISessionInterface $cISession
     *
     * @return void
     */
    public function deleteCISession(CISessionInterface $cISession)
    {
        $this->em->remove($cISession);
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
