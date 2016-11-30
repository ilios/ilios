<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Id\AssignedGenerator;

/**
 * Class BaseManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class BaseManager implements ManagerInterface
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
     * @var Registry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param Registry $registry
     * @param string $class
     */
    public function __construct(Registry $registry, $class)
    {
        $this->registry   = $registry;
        $this->em         = $registry->getManagerForClass($class);
        $this->class      = $class;
    }

    /**
     * Get the repository from the registry
     * We have to do this here because the call to registry::getRepository
     * requires the database to be setup which is a problem for using managers in console commands
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        if (!$this->repository) {
            $this->repository = $this->registry->getRepository($this->class);
        }

        return $this->repository;
    }

    /**
     * @inheritdoc
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @inheritdoc
     */
    public function flushAndClear()
    {
        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        $results = $this->getRepository()->findDTOsBy($criteria, $orderBy, 1);
        return empty($results)?false:$results[0];
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function update(
        $entity,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($entity);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($entity));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        $entity
    ) {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $class = $this->getClass();
        return new $class();
    }
}
