<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AamcPcrsInterface;

/**
 * Class AamcPcrsManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AamcPcrsManager implements AamcPcrsManagerInterface
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
     * @return AamcPcrsInterface
     */
    public function findAamcPcrsBy(
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
     * @return ArrayCollection|AamcPcrsInterface[]
     */
    public function findAamcPcrsesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AamcPcrsInterface $aamcPcrs
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateAamcPcrs(
        AamcPcrsInterface $aamcPcrs,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($aamcPcrs);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($aamcPcrs));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AamcPcrsInterface $aamcPcrs
     */
    public function deleteAamcPcrs(
        AamcPcrsInterface $aamcPcrs
    ) {
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

    /**
     * @return AamcPcrsInterface
     */
    public function createAamcPcrs()
    {
        $class = $this->getClass();
        return new $class();
    }
}
