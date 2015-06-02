<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;

/**
 * Interface CurriculumInventoryExportManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CurriculumInventoryExportManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventoryExportInterface
     */
    public function findCurriculumInventoryExportBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CurriculumInventoryExportInterface[]
     */
    public function findCurriculumInventoryExportsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CurriculumInventoryExportInterface $curriculumInventoryExport
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCurriculumInventoryExport(
        CurriculumInventoryExportInterface $curriculumInventoryExport,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param CurriculumInventoryExportInterface $curriculumInventoryExport
     *
     * @return void
     */
    public function deleteCurriculumInventoryExport(
        CurriculumInventoryExportInterface $curriculumInventoryExport
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return CurriculumInventoryExportInterface
     */
    public function createCurriculumInventoryExport();
}
