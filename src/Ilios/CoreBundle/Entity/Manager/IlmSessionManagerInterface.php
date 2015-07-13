<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\IlmSessionInterface;

/**
 * Interface IlmSessionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface IlmSessionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return IlmSessionInterface
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
     * @return ArrayCollection|IlmSessionInterface[]
     */
    public function findIlmSessionFacetsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param IlmSessionInterface $ilmSessionFacet
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateIlmSessionFacet(
        IlmSessionInterface $ilmSessionFacet,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param IlmSessionInterface $ilmSessionFacet
     *
     * @return void
     */
    public function deleteIlmSessionFacet(
        IlmSessionInterface $ilmSessionFacet
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return IlmSessionInterface
     */
    public function createIlmSession();
}
