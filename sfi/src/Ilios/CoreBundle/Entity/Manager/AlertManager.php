<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\AlertManager as BaseAlertManager;
use Ilios\CoreBundle\Model\AlertInterface;

class AlertManager extends BaseAlertManager
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
     * @return AlertInterface
     */
    public function findAlertBy(array $criteria, array $orderBy = null)
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
     * @return AlertInterface[]|Collection
     */
    public function findAlertsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AlertInterface $alert
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAlert(AlertInterface $alert, $andFlush = true)
    {
        $this->em->persist($alert);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AlertInterface $alert
     *
     * @return void
     */
    public function deleteAlert(AlertInterface $alert)
    {
        $this->em->remove($alert);
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
