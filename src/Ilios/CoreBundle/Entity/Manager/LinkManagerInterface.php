<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\LearningMaterials\LinkInterface;

/**
 * Interface LinkManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager\LearningMaterials
 */
interface LinkManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LinkInterface
     */
    public function findLinkBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|LinkInterface[]
     */
    public function findLinksBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param LinkInterface $link
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateLink(
        LinkInterface $link,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param LinkInterface $link
     *
     * @return void
     */
    public function deleteLink(
        LinkInterface $link
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return LinkInterface
     */
    public function createLink();
}
