<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\PublishEventInterface;

/**
 * PublishEvent manager service.
 * Class PublishEventManager
 * @package Ilios\CoreBundle\Manager
 */
class PublishEventManager implements PublishEventManagerInterface
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
     * @return PublishEventInterface
     */
    public function findPublishEventBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return PublishEventInterface[]|Collection
     */
    public function findPublishEventsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param PublishEventInterface $publishEvent
     * @param bool $andFlush
     */
    public function updatePublishEvent(PublishEventInterface $publishEvent, $andFlush = true)
    {
        $this->em->persist($publishEvent);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function deletePublishEvent(PublishEventInterface $publishEvent)
    {
        $this->em->remove($publishEvent);
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
     * @return PublishEventInterface
     */
    public function createPublishEvent()
    {
        $class = $this->getClass();
        return new $class();
    }
}
