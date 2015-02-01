<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\IlmSessionFacetInterface;

/**
 * Interface IlmSessionFacetManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface IlmSessionFacetManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return IlmSessionFacetInterface
     */
    public function findIlmSessionFacetBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return IlmSessionFacetInterface[]|Collection
     */
    public function findIlmSessionFacetsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     * @param bool $andFlush
     *
     * @return void
     */
     public function updateIlmSessionFacet(IlmSessionFacetInterface $ilmSessionFacet, $andFlush = true);

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     *
     * @return void
     */
    public function deleteIlmSessionFacet(IlmSessionFacetInterface $ilmSessionFacet);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return IlmSessionFacetInterface
     */
    public function createIlmSessionFacet();
}