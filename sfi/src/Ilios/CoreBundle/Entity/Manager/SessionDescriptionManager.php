<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\SessionDescriptionManager as BaseSessionDescriptionManager;
use Ilios\CoreBundle\Model\SessionDescriptionInterface;

class SessionDescriptionManager extends BaseSessionDescriptionManager
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
     * @return SessionDescriptionInterface
     */
    public function findSessionDescriptionBy(array $criteria, array $orderBy = null)
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
     * @return SessionDescriptionInterface[]|Collection
     */
    public function findSessionDescriptionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param SessionDescriptionInterface $sessionDescription
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateSessionDescription(SessionDescriptionInterface $sessionDescription, $andFlush = true)
    {
        $this->em->persist($sessionDescription);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param SessionDescriptionInterface $sessionDescription
     *
     * @return void
     */
    public function deleteSessionDescription(SessionDescriptionInterface $sessionDescription)
    {
        $this->em->remove($sessionDescription);
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
