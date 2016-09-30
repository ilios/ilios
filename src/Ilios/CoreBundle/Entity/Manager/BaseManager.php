<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\Offering;
use Ilios\CoreBundle\Entity\SessionStampableInterface;
use Ilios\CoreBundle\Traits\OfferingsEntityInterface;
use Ilios\CoreBundle\Entity\Session;
use Ilios\CoreBundle\Entity\SessionInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;

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
    public function update(
        $entity,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($entity);
        $this->stamp($entity);

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
        $this->stamp($entity);
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

    protected function stamp($entity)
    {

        if ($entity instanceof TimestampableEntityInterface) {
            $entity->stampUpdate();
        }

        if ($entity instanceof OfferingsEntityInterface) {
            $offerings = $entity->getOfferings();
            $em = $this->registry->getManagerForClass(Offering::class);
            foreach ($offerings as $offering) {
                $offering->stampUpdate();
                $em->persist($offering);
            }
        }

        if ($entity instanceof SessionStampableInterface) {
            $sessions = $entity->getSessions();
            $em = $this->registry->getManagerForClass(Session::class);
            foreach ($sessions as $session) {
                $session->stampUpdate();
                $em->persist($session);
            }
        }
    }
}
