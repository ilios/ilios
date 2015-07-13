<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\IlmSessionInterface;

/**
 * Class IlmSessionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class IlmSessionManager implements IlmSessionManagerInterface
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
     * @return IlmSessionInterface
     */
    public function findIlmSessionFacetBy(
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
     * @return ArrayCollection|IlmSessionInterface[]
     */
    public function findIlmSessionFacetsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param IlmSessionInterface $ilmSessionFacet
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateIlmSessionFacet(
        IlmSessionInterface $ilmSessionFacet,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($ilmSessionFacet);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($ilmSessionFacet));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param IlmSessionInterface $ilmSessionFacet
     */
    public function deleteIlmSessionFacet(
        IlmSessionInterface $ilmSessionFacet
    ) {
        $this->em->remove($ilmSessionFacet);
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
     * @return IlmSessionInterface
     */
    public function createIlmSession()
    {
        $class = $this->getClass();
        return new $class();
    }
}
