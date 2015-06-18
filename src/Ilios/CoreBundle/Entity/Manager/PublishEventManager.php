<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\PublishEventInterface;

/**
 * Class PublishEventManager
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @var Request
     */
    protected $request;

    /**
     * @param Registry $em
     * @param string $class
     */
    public function __construct(Registry $em, $class, Request $request)
    {
        $this->em         = $em->getManagerForClass($class);
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
        $this->request    = $request;
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return PublishEventInterface
     */
    public function findPublishEventBy(
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
     * @return ArrayCollection|PublishEventInterface[]
     */
    public function findPublishEventsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param PublishEventInterface $publishEvent
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updatePublishEvent(
        PublishEventInterface $publishEvent,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($publishEvent);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($publishEvent));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function deletePublishEvent(
        PublishEventInterface $publishEvent
    ) {
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
        $obj = new $class();
        $obj->setMachineIp($this->request->getClientIp());
        return $obj;
    }
}
