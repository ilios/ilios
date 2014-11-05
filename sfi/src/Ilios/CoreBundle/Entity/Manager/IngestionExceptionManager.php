<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\IngestionExceptionManager as BaseIngestionExceptionManager;
use Ilios\CoreBundle\Model\IngestionExceptionInterface;

class IngestionExceptionManager extends BaseIngestionExceptionManager
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
     * @return IngestionExceptionInterface
     */
    public function findIngestionExceptionBy(array $criteria, array $orderBy = null)
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
     * @return IngestionExceptionInterface[]|Collection
     */
    public function findIngestionExceptionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param IngestionExceptionInterface $ingestionException
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateIngestionException(IngestionExceptionInterface $ingestionException, $andFlush = true)
    {
        $this->em->persist($ingestionException);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param IngestionExceptionInterface $ingestionException
     *
     * @return void
     */
    public function deleteIngestionException(IngestionExceptionInterface $ingestionException)
    {
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
}
