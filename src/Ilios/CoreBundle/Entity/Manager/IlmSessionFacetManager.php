<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\IlmSessionFacetInterface;

/**
 * Class IlmSessionFacetManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class IlmSessionFacetManager implements IlmSessionFacetManagerInterface
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
     * @return IlmSessionFacetInterface
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
     * @return ArrayCollection|IlmSessionFacetInterface[]
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
     * @param IlmSessionFacetInterface $ilmSessionFacet
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateIlmSessionFacet(
        IlmSessionFacetInterface $ilmSessionFacet,
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
     * @param IlmSessionFacetInterface $ilmSessionFacet
     */
    public function deleteIlmSessionFacet(
        IlmSessionFacetInterface $ilmSessionFacet
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
     * @return IlmSessionFacetInterface
     */
    public function createIlmSessionFacet()
    {
        $class = $this->getClass();
        return new $class();
    }
}
