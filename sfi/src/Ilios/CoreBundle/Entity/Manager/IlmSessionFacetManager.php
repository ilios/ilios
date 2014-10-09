<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\IlmSessionFacetManager as BaseIlmSessionFacetManager;
use Ilios\CoreBundle\Model\IlmSessionFacetInterface;

class IlmSessionFacetManager extends BaseIlmSessionFacetManager
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
     * @return IlmSessionFacetInterface
     */
    public function findIlmSessionFacetBy(array $criteria, array $orderBy = null)
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
     * @return IlmSessionFacetInterface[]|Collection
     */
    public function findIlmSessionFacetsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateIlmSessionFacet(IlmSessionFacetInterface $ilmSessionFacet, $andFlush = true)
    {
        $this->em->persist($ilmSessionFacet);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     *
     * @return void
     */
    public function deleteIlmSessionFacet(IlmSessionFacetInterface $ilmSessionFacet)
    {
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
}
