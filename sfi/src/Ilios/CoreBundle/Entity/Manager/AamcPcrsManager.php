<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\AamcPcrsManager as BaseAamcPcrsManager;
use Ilios\CoreBundle\Model\AamcPcrsInterface;

class AamcPcrsManager extends BaseAamcPcrsManager
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
     * @return AamcPcrsInterface
     */
    public function findAamcPcrsBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AamcPcrsInterface[]|Collection
     */
    public function findAamcPcrsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AamcPcrsInterface $aamcPcrs
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateAamcPcrs(AamcPcrsInterface $aamcPcrs, $andFlush = true)
    {
        $this->em->persist($aamcPcrs);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AamcPcrsInterface $aamcPcrs
     *
     * @return void
     */
    public function deleteAamcPcrs(AamcPcrsInterface $aamcPcrs)
    {
        $this->em->remove($aamcPcrs);
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
