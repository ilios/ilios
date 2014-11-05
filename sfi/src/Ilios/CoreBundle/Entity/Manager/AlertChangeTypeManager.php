<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\AlertChangeTypeManager as BaseAlertChangeTypeManager;
use Ilios\CoreBundle\Model\AlertChangeTypeInterface;

class AlertChangeTypeManager extends BaseAlertChangeTypeManager
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
     * @return AlertChangeTypeInterface
     */
    public function findAlertChangeTypeBy(array $criteria, array $orderBy = null)
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
     * @return AlertChangeTypeInterface[]|Collection
     */
    public function findAlertChangeTypesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAlertChangeType(AlertChangeTypeInterface $alertChangeType, $andFlush = true)
    {
        $this->em->persist($alertChangeType);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AlertChangeTypeInterface $alertChangeType
     *
     * @return void
     */
    public function deleteAlertChangeType(AlertChangeTypeInterface $alertChangeType)
    {
        $this->em->remove($alertChangeType);
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
