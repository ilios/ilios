<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\IlmSessionFacetInterface;

/**
 * Interface IlmSessionFacetManagerInterface
 */
interface IlmSessionFacetManagerInterface
{
    /** 
     *@return IlmSessionFacetInterface
     */
    public function createIlmSessionFacet();

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
     * @param int $limit
     * @param int $offset
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
}
