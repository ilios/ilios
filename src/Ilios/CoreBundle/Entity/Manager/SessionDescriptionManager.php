<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SessionDescriptionInterface;

/**
 * Class SessionDescriptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionDescriptionManager implements SessionDescriptionManagerInterface
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
     * @return SessionDescriptionInterface
     */
    public function findSessionDescriptionBy(
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
     * @return ArrayCollection|SessionDescriptionInterface[]
     */
    public function findSessionDescriptionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param SessionDescriptionInterface $sessionDescription
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateSessionDescription(
        SessionDescriptionInterface $sessionDescription,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($sessionDescription);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($sessionDescription));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param SessionDescriptionInterface $sessionDescription
     */
    public function deleteSessionDescription(
        SessionDescriptionInterface $sessionDescription
    ) {
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

    /**
     * @return SessionDescriptionInterface
     */
    public function createSessionDescription()
    {
        $class = $this->getClass();
        return new $class();
    }
}
