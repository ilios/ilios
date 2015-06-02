<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\IngestionExceptionInterface;

/**
 * Class IngestionExceptionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class IngestionExceptionManager implements IngestionExceptionManagerInterface
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
     * @return IngestionExceptionInterface
     */
    public function findIngestionExceptionBy(
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
     * @return ArrayCollection|IngestionExceptionInterface[]
     */
    public function findIngestionExceptionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param IngestionExceptionInterface $ingestionException
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateIngestionException(
        IngestionExceptionInterface $ingestionException,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($ingestionException);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($ingestionException));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param IngestionExceptionInterface $ingestionException
     */
    public function deleteIngestionException(
        IngestionExceptionInterface $ingestionException
    ) {
        $this->em->remove($ingestionException);
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
     * @return IngestionExceptionInterface
     */
    public function createIngestionException()
    {
        $class = $this->getClass();
        return new $class();
    }
}
