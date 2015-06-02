<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\IlmSessionFacetInterface;

/**
 * Interface IlmSessionFacetManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface IlmSessionFacetManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return IlmSessionFacetInterface
     */
    public function findIlmSessionFacetBy(
        array $criteria,
        array $orderBy = null
    );

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
    );

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateIlmSessionFacet(
        IlmSessionFacetInterface $ilmSessionFacet,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     *
     * @return void
     */
    public function deleteIlmSessionFacet(
        IlmSessionFacetInterface $ilmSessionFacet
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return IlmSessionFacetInterface
     */
    public function createIlmSessionFacet();
}
