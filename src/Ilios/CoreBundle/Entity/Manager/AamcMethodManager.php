<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\AamcMethodInterface;

/**
 * Class AamcMethodManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class AamcMethodManager implements AamcMethodManagerInterface
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
     * @return AamcMethodInterface
     */
    public function findAamcMethodBy(
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
     * @return ArrayCollection|AamcMethodInterface[]
     */
    public function findAamcMethodsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param AamcMethodInterface $aamcMethod
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateAamcMethod(
        AamcMethodInterface $aamcMethod,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($aamcMethod);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($aamcMethod));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param AamcMethodInterface $aamcMethod
     */
    public function deleteAamcMethod(
        AamcMethodInterface $aamcMethod
    ) {
        $this->em->remove($aamcMethod);
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
     * @return AamcMethodInterface
     */
    public function createAamcMethod()
    {
        $class = $this->getClass();
        return new $class();
    }
}
