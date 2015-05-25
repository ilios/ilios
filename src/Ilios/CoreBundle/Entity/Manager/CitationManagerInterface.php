<?php

namespace Ilios\CoreBundle\Entity\Manager\LearningMaterials;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CitationInterface;

/**
 * Interface CitationManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager\LearningMaterials
 */
interface CitationManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CitationInterface
     */
    public function findCitationBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CitationInterface[]
     */
    public function findCitationsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CitationInterface $citation
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCitation(
        CitationInterface $citation,
        $andFlush = true,
        $forceId  = false
    );

    /**
     * @param CitationInterface $citation
     *
     * @return void
     */
    public function deleteCitation(
        CitationInterface $citation
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return CitationInterface
     */
    public function createCitation();
}
