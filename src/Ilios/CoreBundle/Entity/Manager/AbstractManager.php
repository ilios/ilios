<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * Class AbstractManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
abstract class AbstractManager implements ManagerInterface
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
     * We have to do this here becuase the call to registry::getRepository
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
}
